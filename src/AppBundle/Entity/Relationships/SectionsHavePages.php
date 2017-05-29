<?php

namespace AppBundle\Entity\Relationships;

// section
use AppBundle\Entity\Section;
// page
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait SectionsHavePages
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Page` entity to implement `$sections` property with `ManyToMany` and `inversedBy="pages"` annotation.
 * 2. Requires `Page` entity to implement `linkSection` and `unlinkSection` methods.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->pages = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait SectionsHavePages
{
    /**
     * @var array|Page[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="sections", cascade={"persist"})
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
        $this->pages = $pages;

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
        $page->linkSection($this);

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
        $page->unlinkSection($this);

        return $this;
    }

    /**
     * @param Page $page
     */
    public function linkPage(Page $page)
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
        }
    }

    /**
     * @param Page $page
     */
    public function unlinkPage(Page $page)
    {
        $this->pages->removeElement($page);
    }
}
