<?php

namespace TangoMan\ListManagerBundle\Model;

/**
 * Class InputBuilder
 *
 * @package TangoMan\ListManagerBundle\Model
 */
class InputBuilder
{
    /**
     * @var array
     */
    private $validTypes = [
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
    ];

    /**
     * @var string
     */
    private $type;

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
    private $default;

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
        if (in_array($type, $this->validTypes)) {
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
     * @param String $name
     *
     * @return bool
     */
    public function hasOption($name)
    {
        $flag = false;
        foreach ($this->options as $option) {
            if ($option['name'] = $name) {
                $flag = true;
            }
        }

        return $flag;
    }

    /**
     * @param String $name
     * @param String $value
     *
     * @return $this
     */
    public function addOption($name, $value)
    {
        // Value is replaced when option exists
        foreach ($this->options as $option) {
            if ($option['name'] = $name) {
                $option['value'] = $value;

                return $this;
            }
        }

        $this->options[] = [
            'name'  => $name,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @param String $name
     *
     * @return $this
     */
    public function removeOption($name)
    {
        foreach ($this->options as $option) {
            if ($option['name'] = $name) {
                unset($this->options[$option]);
            }
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
