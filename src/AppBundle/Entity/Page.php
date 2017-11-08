<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TangoMan\EntityHelper\Traits\HasRelationships;
use TangoMan\EntityHelper\Traits\HasSummary;
use TangoMan\EntityHelper\Traits\HasTitle;
use TangoMan\EntityHelper\Traits\HasViews;
use TangoMan\EntityHelper\Traits\Publishable;
use TangoMan\EntityHelper\Traits\Sluggable;
use TangoMan\EntityHelper\Traits\Timestampable;

/**
 * Class Page
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Page
{
//    use Relationships\PagesHaveSections;
//    use Relationships\PagesHaveSites;
//    use Relationships\PagesHaveTags;
    use HasRelationships;


    use HasSummary;
    use HasTitle;
    use HasViews;
    use Publishable;
    use Sluggable;
    use Timestampable;

    /**
     * @var int Page id
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", inversedBy="pages", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sections = [];

    /**
     * @var array|Site[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Site", mappedBy="pages", cascade={"persist"})
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $sites = [];

    /**
     * @var array|Tag[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="pages", cascade={"persist"})
     */
    private $tags = [];

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
        $this->sites = new ArrayCollection();
        $this->tags = new ArrayCollection();
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
