<?php

namespace TangoMan\ListManagerBundle\Model;

/**
 * Class SearchOption
 *
 * @package TangoMan\ListManagerBundle\Model
 */
class SearchOption
{
    /**
     * @var String
     */
    private $name;

    /**
     * @var String
     */
    private $value;

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param String $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return String
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param String $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
