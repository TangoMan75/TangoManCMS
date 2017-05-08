<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasPages
 * 
 * This trait defines the OWNING side of the relationship.
 * 
 * 1. Page entity must implement inversed property and methods.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->pages = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasPages
{
    /**
     * @var Page[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", inversedBy="contents")
     */
    private $pages = [];

    /**
     * @param Page[]|ArrayCollection $pages
     *
     * @return $this
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * @return Page[]
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param Page $page
     *
     * @return bool
     */
    public function hasPage(Page $page)
    {
        if (in_array($page, (array)$this->pages)) {
            return true;
        }

        return false;
    }

    /**
     * @param Page $page
     *
     * @return $this
     */
    public function addPage(Page $page)
    {
        if (!in_array($page, (array)$this->pages)) {
            $this->pages[] = $page;
        }

        return $this;
    }

    /**
     * @param page $page
     *
     * @return $this
     */
    public function removePage(page $page)
    {
        $this->pages->removeElement($page);

        return $this;
    }
}
