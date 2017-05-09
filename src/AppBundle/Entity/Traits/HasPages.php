<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasPages
 *
 * This trait defines the INVERSE side of the relationship.
 *
 * 1. Requires `Page` entity to implement `$items` property with `ManyToMany` and `mappedBy="items"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->pages = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasPages
{
    /**
     * @var array|Page[]|ArrayCollection
     */
    private $pages = [];

    /**
     * @param array|Page[]|ArrayCollection $pages
     *
     * @return $this
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * @return array|Page[]|ArrayCollection
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
     * @param Page $page
     *
     * @return $this
     */
    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);

        return $this;
    }
}
