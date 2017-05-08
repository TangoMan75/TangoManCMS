<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasItems
 *
 * This trait defines the INVERSE side of the relationship.
 * 
 * 1. Requires entity with "HasItems" trait to own "items" property marked with "ManyToMany", 
 *     "mappedBy=`tags`" annotation defining desired relationships with target entity.
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
     * @param $item
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
     * @param $item
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
     * @param $item
     *
     * @return $this
     */
    public function removeItem($item)
    {
        $this->items->removeElement($item);

        return $this;
    }
}
