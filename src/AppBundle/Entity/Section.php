<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TangoMan\EntityHelper\HasSummary;
use TangoMan\EntityHelper\HasTitle;
use TangoMan\EntityHelper\HasType;
use TangoMan\EntityHelper\Publishable;
use TangoMan\EntityHelper\Sluggable;
use TangoMan\EntityHelper\Timestampable;

/**
 * Class Section
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="section")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Section
{
    use Relationships\SectionsHavePages;
    use Relationships\SectionsHavePosts;
    use Relationships\SectionsHaveTags;

    use HasSummary;
    use HasTitle;
    use HasType;
    use Publishable;
    use Sluggable;
    use Timestampable;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mediaCount;

    /**
     * Section constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->posts = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->tags = new ArrayCollection();

        $this->type = 'default';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPostCount()
    {
        if (!$this->postCount) {
            return 0;
        }

        return $this->postCount;
    }

    /**
     * @param int $postCount
     *
     * @return $this
     */
    public function setPostCount($postCount)
    {
        $this->postCount = $postCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMediaCount()
    {
        if (!$this->mediaCount) {
            return 0;
        }

        return $this->mediaCount;
    }

    /**
     * @param $mediaCount
     *
     * @return $this
     */
    public function setMediaCount($mediaCount)
    {
        $this->mediaCount = $mediaCount;

        return $this;
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
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
