<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Page;

/**
 * Class Publishable
 *
 * @TODO    publish into sections
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Publishable
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $published = false;

    /**
     * @var Page Post page
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Page", inversedBy="items")
     */
    private $page;

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     *
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param Page $page
     *
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }
}