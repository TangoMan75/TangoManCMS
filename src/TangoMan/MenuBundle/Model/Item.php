<?php

namespace TangoMan\MenuBundle\Model;

use TangoMan\MenuBundle\Model\Menu;

/**
 * Class Item
 *
 * @package TangoMan\MenuBundle\Model
 */
class Item implements \JsonSerializable
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var bool
     */
    private $divider;

    /**
     * @var Menu
     */
    private $subMenu;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param string $route
     *
     * @return $this
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param string $query
     *
     * @return Item
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDivider()
    {
        return $this->divider;
    }

    /**
     * @param bool $divider
     *
     * @return Item
     */
    public function setDivider($divider)
    {
        $this->divider = $divider;

        return $this;
    }

    /**
     * @return Menu
     */
    public function getSubMenu()
    {
        return $this->subMenu;
    }

    /**
     * @param Menu $subMenu
     *
     * @return $this
     */
    public function setSubMenu(Menu $subMenu)
    {
        $this->subMenu = $subMenu;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];

        if ($this->label) {
            $json['label'] = $this->label;
        }

        if ($this->route) {
            $json['route'] = $this->route;
        }

        if ($this->query) {
            $json['query'] = $this->query;
        }

        if ($this->icon) {
            $json['icon'] = $this->icon;
        }

        if ($this->divider) {
            $json['divider'] = $this->divider;
        }

        if ($this->subMenu) {
            $json['subMenu'] = $this->subMenu->jsonSerialize();
        }

        return $json;
    }
}
