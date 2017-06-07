<?php

namespace AppBundle\Entity\Relationships;

// category
use AppBundle\Entity\Category;
// section
use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait SectionsHaveCategories
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 1. Requires owned `Category` entity to implement `$sections` property with `ManyToMany` and `mappedBy="categories"` annotation.
 * 2. Requires owned `Category` entity to implement `linkSection` and `unlinkSection` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->categories = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait SectionsHaveCategories
{
    /**
     * @var array|Category[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Category", inversedBy="sections", cascade={"persist"})
     */
    private $categories = [];

    /**
     * @param array|Category[]|ArrayCollection $categories
     *
     * @return $this
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return array|Category[]|ArrayCollection $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Category $category
     *
     * @return bool
     */
    public function hasCategory(Category $category)
    {
        if ($this->categories->contains($category)) {
            return true;
        }

        return false;
    }

    /**
     * @param Category $category
     *
     * @return $this
     */
    public function addCategory(Category $category)
    {
        $this->linkCategory($category);
        $category->linkSection($this);

        return $this;
    }

    /**
     * @param Category $category
     *
     * @return $this
     */
    public function removeCategory(Category $category)
    {
        $this->unlinkCategory($category);
        $category->unlinkSection($this);

        return $this;
    }

    /**
     * @param Category $category
     */
    public function linkCategory(Category $category)
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
        }
    }

    /**
     * @param Category $category
     */
    public function unlinkCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }
}
