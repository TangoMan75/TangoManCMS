<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use TangoMan\EntityHelper\Categorized;
use TangoMan\EntityHelper\Embeddable;
use TangoMan\EntityHelper\HasSummary;
use TangoMan\EntityHelper\HasText;
use TangoMan\EntityHelper\HasTitle;
use TangoMan\EntityHelper\HasType;
use TangoMan\EntityHelper\HasViews;
use TangoMan\EntityHelper\Publishable;
use TangoMan\EntityHelper\Sluggable;
use TangoMan\EntityHelper\Timestampable;
use TangoMan\EntityHelper\UploadableDocument;
use TangoMan\EntityHelper\UploadableImage;

/**
 * Class Post
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="post")
 *
 * @Vich\Uploadable
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Post
{
    use Relationships\PostHasComments;
    use Relationships\PostsHaveSections;
    use Relationships\PostsHaveTags;
    use Relationships\PostsHaveUser;
    use Relationships\PostHasVotes;

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
     * Post constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
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