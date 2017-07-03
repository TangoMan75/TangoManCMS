<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait SitesHavePages
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 1. Requires owned `Page` entity to implement `$sites` property with `ManyToMany` and `mappedBy="pages"` annotation.
 * 2. Requires owned `Page` entity to implement `linkSite` and `unlinkSite` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->pages = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait SitesHavePages
{
    /**
     * @var array|Page[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", inversedBy="sites", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $pages = [];

    /**
     * @param array|Page[]|ArrayCollection $pages
     *
     * @return $this
     */
    public function setPages($pages)
    {
        foreach (array_diff($this->pages, $pages) as $page) {
            $this->unlinkPage($page);
        }

        foreach ($pages as $page) {
            $this->linkPage($page);
        }

        return $this;
    }

    /**
     * @return array|Page[]|ArrayCollection $pages
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
        if ($this->pages->contains($page)) {
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
        $this->linkPage($page);
        $page->linkSite($this);

        return $this;
    }

    /**
     * @param Page $page
     *
     * @return $this
     */
    public function removePage(Page $page)
    {
        $this->unlinkPage($page);
        $page->unlinkSite($this);

        return $this;
    }

    /**
     * @param Page $page
     */
    public function linkPage(Page $page)
    {
        if (!$this->pages->contains($page)) {
            if ($page->getType() == 'gallery') {
                $this->galleryCount = ++$this->galleryCount;
            } else {
                $this->pageCount = ++$this->pageCount;
            }
            $this->pages[] = $page;
        }
    }

    /**
     * @param Page $page
     */
    public function unlinkPage(Page $page)
    {
        if ($this->pages->contains($page)) {
            if ($page->getType() == 'gallery') {
                $this->galleryCount = --$this->galleryCount;
            } else {
                $this->pageCount = --$this->pageCount;
            }
        }
        $this->pages->removeElement($page);
    }
}
