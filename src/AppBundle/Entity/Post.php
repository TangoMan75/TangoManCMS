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
    use Traits\Embeddable;
    use Traits\Publishable;
    use Traits\Sluggable;
    use Traits\Taggable;
    use Traits\Timestampable;
    use Traits\Titleable;
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var Section[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", mappedBy="posts")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sections = [];

    /**
     * @var Comment[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="post", cascade={"remove"})
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
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function addPage($page)
    {
        if (!in_array($page, $this->sections)) {
            $this->sections[] = $page;
        }

        return $this;
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function removePage($page)
    {
        $this->sections->removeElement($page);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment[] $comments Comment list
     *
     * @return Post
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
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