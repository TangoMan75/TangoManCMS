<?php

/*
 * This file is part of the TangoManCMS package.
 *
 * (c) Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
 * Class Site
 * @ORM\Table(name="site")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SiteRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>core/pdo.php
 */
class Site
{

    use HasRelationships;
    use HasSummary;
    use HasTitle;
    use HasViews;
    use Publishable;
    use Sluggable;
    use Timestampable;

    /**
     * @var int Site id
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Page[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", inversedBy="sites",
     *                                                       cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $pages;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="sites",
     *                                                      cascade={"persist"})
     */
    private $tags;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pageCount;

    /**
     * Site constructor.
     */
    public function __construct()
    {
        $this->created  = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->pages    = new ArrayCollection();
        $this->tags     = new ArrayCollection();
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
    public function getPageCount()
    {
        if ( ! $this->pageCount) {
            return 0;
        }

        return $this->pageCount;
    }

    /**
     * @param $pageCount
     *
     * @return $this
     */
    public function setPageCount($pageCount)
    {
        $this->pageCount = $pageCount;

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
