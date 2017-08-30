<?php

namespace TangoMan\MenuBundle\Model;

use TangoMan\MenuBundle\Model\Item;

/**
 * Class Menu
 *
 * @package TangoMan\MenuBundle\Model
 */
class Menu implements \JsonSerializable
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @param array $items
     *
     * @return $this
     */
    public function setItems($items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * @return array $items
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param \TangoMan\MenuBundle\Model\Item $item
     *
     * @return bool
     */
    public function hasItem(Item $item)
    {
        if (in_array($item, $this->items)) {
            return true;
        }

        return false;
    }

    /**
     * @param \TangoMan\MenuBundle\Model\Item $item
     *
     * @return $this
     */
    public function addItem(Item $item)
    {
        if (!$this->hasItem($item)) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * @param \TangoMan\MenuBundle\Model\Item $item
     *
     * @return $this
     */
    public function removeItem(Item $item)
    {
        $items = $this->items;

        if ($this->hasItem($item)) {
            $remove[] = $item;
            $this->items = array_diff($items, $remove);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->jsonSerialize();
        }

        return [
            'items' => $items,
        ];
    }
}
