<?php

namespace AppBundle\Entity\Relationships;

// tag
use AppBundle\Entity\Tag;
// page
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait TagsHavePages
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Page` entity to implement `$tags` property with `ManyToMany` and `inversedBy="pages"` annotation.
 * 2. Requires `Page` entity to implement `linkTag` and `unlinkTag` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->pages = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
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
     * @return array|Page[]|ArrayCollection $pages
     */
    public function getPages()
    {
        return $this->pages;
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
