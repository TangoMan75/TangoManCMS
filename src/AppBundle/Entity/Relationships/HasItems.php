<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasItems
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 1. Requires owned `Item` entity to implement `$tags` property with `ManyToMany` and `inversedBy="items"` annotation.
 * 2. Requires owned `Item` entity to implement `linkTag` and `unlinkTag` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->items = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
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
     * @return $this
     */
    public function addItem($item)
    {
        $this->linkItem($item);
        $item->linkTag($this);

        return $this;
    }

    /**
     * @return $this
     */
    public function removeItem($item)
    {
        $this->unlinkItem($item);
        $item->unlinkTag($this);

        return $this;
    }

    public function linkItem($item)
    {
        if (!in_array($item, (array)$this->items)) {
            $this->items[] = $item;
        }
    }

    public function unlinkItem($item)
    {
        $this->items->removeElement($item);
    }
}
