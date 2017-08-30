<?php

namespace TangoMan\ListManagerBundle\Model;

/**
 * Class OrderField
 *
 * @package TangoMan\ListManagerBundle\Model
 */
class OrderField implements \JsonSerializable
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $way;

    /**
     * @var string
     */
    private $route;

    /**
     * @var integer
     */
    private $colspan;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

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
    public function getWay()
    {
        return $this->way;
    }

    /**
     * @param string $way
     *
     * @return $this
     */
    public function setWay($way)
    {
        $way = strtoupper($way);
        if ($way == 'ASC' || $way == 'DESC') {
            $this->way = $way;
        } else {
            $this->way = 'ASC';
        }

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
     * @return int
     */
    public function getColspan()
    {
        return $this->colspan;
    }

    /**
     * @param int $colspan
     *
     * @return $this
     */
    public function setColspan($colspan)
    {
        $this->colspan = $colspan;

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [
            'name'  => $this->name,
            'label' => $this->label,
        ];

        if ($this->way) {
            $json['way'] = $this->way;
        }

        if ($this->route) {
            $json['route'] = $this->route;
        }

        if ($this->colspan) {
            $json['colspan'] = $this->colspan;
        }

        return $json;
    }
}
