<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Post
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="post")
 * @Vich\Uploadable
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Post
{
    use Traits\Categorized;
    use Traits\Commentable;
    use Traits\Embeddable;
    use Traits\HasSections;
    use Traits\HasText;
    use Traits\HasTitle;
    use Traits\HasUser;
    use Traits\Publishable;
    use Traits\Sluggable;
    use Traits\Taggable;
    use Traits\Timestampable;
    use Traits\UploadableDocument;
    use Traits\UploadableImage;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts", cascade={"persist"})
     */
    private $user;

    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", inversedBy="posts", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sections = [];

    /**
     * @var array|Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="post", cascade={"persist", "remove"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $comments = [];

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     * @return $this
     */
    public function setDefaults()
    {
        if (!$this->title) {
            $this->setTitle($this->created->format('d/m/Y H:i:s'));
        }

        if (!$this->slug) {
            $this->setUniqueSlug($this->title);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}