<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait TagsHavePages
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Page` entity to implement `$tags` property with `ManyToMany` and `inversedBy="tags"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->pages = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait TagsHavePages
{
    /**
     * @var array|Page[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="tags", cascade={"persist"})
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
        $this->linkPage($page);
        $page->linkTag($this);

        return $this;
    }

    /**
     * @param Page $page
     */
    public function linkPage(Page $page)
    {
        if (!in_array($page, (array)$this->pages)) {
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
        $page->unlinkTag($this);

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
