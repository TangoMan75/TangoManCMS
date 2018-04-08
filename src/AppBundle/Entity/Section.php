<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
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
use TangoMan\EntityHelper\Traits\HasType;
use TangoMan\EntityHelper\Traits\Publishable;
use TangoMan\EntityHelper\Traits\Sluggable;
use TangoMan\EntityHelper\Traits\Timestampable;
use TangoMan\RelationshipBundle\Traits\HasRelationships;

/**
 * Class Section
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Section
{

    use HasRelationships;
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
     * @var Page[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="sections",
     *                                                       cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $pages;

    /**
     * @var Post[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", inversedBy="sections",
     *                                                       cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="sections",
     *                                                      cascade={"persist"})
     */
    private $tags;

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
        $this->posts    = new ArrayCollection();
        $this->pages    = new ArrayCollection();
        $this->tags     = new ArrayCollection();
        $this->created  = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();

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
        if ( ! $this->postCount) {
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
        if ( ! $this->mediaCount) {
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
