<?php

namespace TangoMan\ListManagerBundle\Model;

use TangoMan\ListManagerBundle\Model\SearchOption;

/**
 * Class SearchInput
 *
 * @package TangoMan\ListManagerBundle\Model
 */
class SearchInput
{
    /**
     * @var string
     */
    private $type = 'text';

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
    private $default = 'Tous';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        if (in_array(
            $type,
            [
                'button',
                'color',
                'date',
                'datetime',
                'datetime-local',
                'email',
                'file',
                'keygen',
                'month',
                'number',
                'password',
                'phone',
                'range',
                'reset',
                'search',
                'submit',
                'tel',
                'text',
                'time',
                'url',
                'week',
            ]
        )) {
            $this->type = $type;
        }

        return $this;
    }

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
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->type = 'select';
        $this->options = $options;

        return $this;
    }

    /**
     * @return array $options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\SearchOption $option
     *
     * @return bool
     */
    public function hasOption(SearchOption $option)
    {
        if (in_array($option, $this->options)) {
            return true;
        }

        return false;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\SearchOption $option
     *
     * @return $this
     */
    public function addOption(SearchOption $option)
    {
        $this->type = 'select';
        if (!$this->hasOption($option)) {
            $this->options[] = $option;
        }

        return $this;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\SearchOption $option
     *
     * @return $this
     */
    public function removeOption(SearchOption $option)
    {
        $options = $this->options;

        if ($this->hasOption($option)) {
            $remove[] = $option;
            $this->options = array_diff($options, $remove);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param string $default
     *
     * @return $this
     */
    public function setDefault($default)
    {
        $this->default = $default;

        return $this;
    }

}
