<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Page
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="page")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Page
{
    use Relationships\PagesHaveSections;
    use Relationships\PagesHaveTags;

    use Traits\HasSummary;
    use Traits\HasTitle;
    use Traits\HasViews;
    use Traits\Publishable;
    use Traits\Sluggable;
    use Traits\Timestampable;

    /**
     * @var int Page id
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $galleryCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sectionCount;

    /**
     * Section constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->sections = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->stats = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getGalleryCount()
    {
        if (!$this->galleryCount) {
            return 0;
        }

        return $this->galleryCount;
    }

    /**
     * @param int $galleryCount
     *
     * @return $this
     */
    public function setGalleryCount($galleryCount)
    {
        $this->galleryCount = $galleryCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getSectionCount()
    {
        if (!$this->sectionCount) {
            return 0;
        }

        return $this->sectionCount;
    }

    /**
     * @param $sectionCount
     *
     * @return $this
     */
    public function setSectionCount($sectionCount)
    {
        $this->sectionCount = $sectionCount;

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
