<?php

namespace TangoMan\FrontBundle\Model;

use TangoMan\FrontBundle\Model\SearchOption;

/**
 * Class SearchInput
 *
 * @package TangoMan\FrontBundle\Model
 */
class SearchInput implements \JsonSerializable
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
     * Label to be displayed
     *
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $placeholder;

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
                'divider',
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
                'select',
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
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return SearchInput
     */
    public function setClass($class)
    {
        $this->class = $class;

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
     * @return SearchInput
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param string $placeholder
     *
     * @return SearchInput
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;

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
     * @param \TangoMan\FrontBundle\Model\SearchOption $option
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
     * @param \TangoMan\FrontBundle\Model\SearchOption $option
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
     * @param \TangoMan\FrontBundle\Model\SearchOption $option
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
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];

        $options = [];
        foreach ($this->options as $option) {
            $options[] = $option->jsonSerialize();
        }

        if ($this->type) {
            $json['type'] = $this->type;
        }

        if ($this->name) {
            $json['name'] = $this->name;
        }

        if ($this->placeholder) {
            $json['placeholder'] = $this->placeholder;
        }

        if ($this->label) {
            $json['label'] = $this->label;
        }

        if ($this->class) {
            $json['class'] = $this->class;
        }

        if ($this->icon) {
            $json['icon'] = $this->icon;
        }

        if ($this->type == 'select') {
            $json['options'] = $options;
        }

        return $json;
    }
}
