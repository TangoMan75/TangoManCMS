<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait StatHasPages
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 
 * 1. Requires `Page` entity to implement `$stat` property with `ManyToOne` and `inversedBy="pages"` annotation.
 * 2. Requires `Page` entity to implement linkStat(Stat $stat) public method.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->pages = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait StatHasPages
{
    /**
     * @var array|Page[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Page", mappedBy="stat", cascade={"persist"})
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
        $page->linkStat($this);

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
     *
     * @return $this
     */
    public function removePage(Page $page)
    {
        $this->unlinkPage($page);
        $page->unlinkStat($this);

        return $this;
    }

    /**
     * @param Page $page
     */
    public function unlinkPage(Page $page)
    {
        $this->pages->removeElement($page);
    }
}
