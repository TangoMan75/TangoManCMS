<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Post
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PostRepository")
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="post")
 * @Vich\Uploadable
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Post
{
    use Relationships\PostHasComments;
    use Relationships\PostHasSections;
    use Relationships\PostHasUser;
    use Relationships\Taggable;

    use Traits\Categorized;
    use Traits\Embeddable;
    use Traits\HasSummary;
    use Traits\HasText;
    use Traits\HasTitle;
    use Traits\HasType;
    use Traits\Publishable;
    use Traits\Sluggable;
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
     * @var Stats
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Stats", inversedBy="posts", fetch="EAGER", cascade={"remove"})
     */
    private $stats;

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
     * @return Stats
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param Stats $stats
     *
     * @return $this
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    private function setDefaults()
    {
        if (!$this->title) {
            $this->setTitle($this->created->format('d/m/Y H:i:s'));
        }

        if (!$this->slug) {
            $this->setUniqueSlug($this->title);
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