<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TangoMan\EntityHelper\Traits\HasSummary;
use TangoMan\EntityHelper\Traits\HasTitle;
use TangoMan\EntityHelper\Traits\HasViews;
use TangoMan\EntityHelper\Traits\Publishable;
use TangoMan\EntityHelper\Traits\Sluggable;
use TangoMan\EntityHelper\Traits\Timestampable;
use TangoMan\RelationshipBundle\Traits\HasRelationships;

/**
 * Class Page
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>
 * @package AppBundle\Entity
 */
class Page
{

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
     * @var Section[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", inversedBy="pages",
     *                                                          cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sections;

    /**
     * @var Site[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Site", mappedBy="pages",
     *                                                       cascade={"persist"})
     * @ORM\OrderBy({"id"="DESC"})
     */
    private $sites;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="pages",
     *                                                      cascade={"persist"})
     */
    private $tags;

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
        $this->sections = new ArrayCollection();
        $this->sites    = new ArrayCollection();
        $this->tags     = new ArrayCollection();
        $this->created  = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
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
        if ( ! $this->galleryCount) {
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
        if ( ! $this->sectionCount) {
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
        if ( ! $this->title) {
            $this->setTitle($this->created->format('d/m/Y H:i:s'));
        }

        if ( ! $this->slug) {
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
