<?php

namespace TangoMan\ListManagerBundle\Model;

use TangoMan\ListManagerBundle\Model\OrderField;

/**
 * Class Fields
 *
 * @package TangoMan\ListManagerBundle\Model
 */
class Fields implements \JsonSerializable
{


    /**
     * @var array
     */
    private $fields = [];

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @return array $fields
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\OrderField $field
     *
     * @return bool
     */
    public function hasField(OrderField $field)
    {
        if (in_array($field, $this->fields)) {
            return true;
        }

        return false;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\OrderField $field
     *
     * @return $this
     */
    public function addField(OrderField $field)
    {
        if (!$this->hasField($field)) {
            $this->fields[] = $field;
        }

        return $this;
    }

    /**
     * @param \TangoMan\ListManagerBundle\Model\OrderField $field
     *
     * @return $this
     */
    public function removeField(OrderField $field)
    {
        $fields = $this->fields;

        if ($this->hasField($field)) {
            $remove[] = $field;
            $this->fields = array_diff($fields, $remove);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $fields = [];
        foreach ($this->fields as $field) {
            $fields[] = $field->jsonSerialize();
        }

        return [
            'fields' => $fields,
        ];
    }
}
