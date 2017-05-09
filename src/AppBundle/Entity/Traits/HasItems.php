<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasItems
 *
 * This trait defines the INVERSE side of the relationship.
 *
 * 1. Requires owned `Item` entity to implement `$owners` property with `ManyToMany` and `inversedBy="items"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->items = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasItems
{
    /**
     * @var array|ArrayCollection
     */
    private $items = [];

    /**
     * @param array|ArrayCollection $items
     *
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return array|ArrayCollection $items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array $item
     *
     * @return bool
     */
    public function hasItem($item)
    {
        if (in_array($item, (array)$this->items)) {
            return true;
        }

        return false;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    public function addItem($item)
    {
        if (!in_array($item, (array)$this->items)) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * @param array $item
     *
     * @return $this
     */
    public function removeItem($item)
    {
        $this->items->removeElement($item);

        return $this;
    }
}
