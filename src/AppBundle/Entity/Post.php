<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TangoMan\EntityHelper\Traits\HasRelationships;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use TangoMan\EntityHelper\Traits\Categorized;
use TangoMan\EntityHelper\Traits\Embeddable;
use TangoMan\EntityHelper\Traits\HasSummary;
use TangoMan\EntityHelper\Traits\HasText;
use TangoMan\EntityHelper\Traits\HasTitle;
use TangoMan\EntityHelper\Traits\HasType;
use TangoMan\EntityHelper\Traits\HasViews;
use TangoMan\EntityHelper\Traits\Publishable;
use TangoMan\EntityHelper\Traits\Sluggable;
use TangoMan\EntityHelper\Traits\Timestampable;
use TangoMan\EntityHelper\Traits\UploadableDocument;
use TangoMan\EntityHelper\Traits\UploadableImage;

/**
 * Class Post
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Vich\Uploadable
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Post
{
//    use Relationships\PostHasComments;
//    use Relationships\PostsHaveSections;
//    use Relationships\PostsHaveTags;
//    use Relationships\PostsHaveUser;
//    use Relationships\PostHasVotes;
    use HasRelationships;

    use Categorized;
    use Embeddable;
    use HasSummary;
    use HasText;
    use HasTitle;
    use HasType;
    use HasViews;
    use Publishable;
    use Sluggable;
    use Timestampable;
    use UploadableDocument;
    use UploadableImage;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array|Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="post", cascade={"persist", "remove"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $comments = [];

    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", mappedBy="posts", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sections = [];

    /**
     * @var array|Tag[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="posts", cascade={"persist"})
     */
    private $tags = [];

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts", cascade={"persist"}, fetch="EAGER")
     */
    private $user;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->sections = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->votes = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setDefaults()
    {
        if (!$this->title) {
            $this->setTitle($this->created->format('d/m/Y H:i:s'));
        }

        if (!$this->slug) {
            $this->setUniqueSlug($this->title);
        }

        if (!$this->type) {
            $this->setType('post');
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}