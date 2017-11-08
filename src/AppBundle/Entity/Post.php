<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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