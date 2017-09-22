<?php

namespace TangoMan\FrontBundle\Model;

use TangoMan\FrontBundle\Model\Button;

/**
 * Class Button
 *
 * @package TangoMan\FrontBundle\Model
 */
class ButtonGroup implements \JsonSerializable
{
    /**
     * Button class
     * e.g: btn btn-primary
     *
     * @var string
     */
    private $class;

    /**
     * Roles granted privilege to see button group
     * (null = no restrictions)
     * e.g: ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']
     *
     * @var array
     */
    private $roles = [];

    /**
     * Button collection
     *
     * @var array
     */
    private $buttons = [];

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
     * @return ButtonGroup
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array $roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param String $role
     *
     * @return bool
     */
    public function hasRole($role)
    {
        if (in_array($role, $this->roles)) {
            return true;
        }

        return false;
    }

    /**
     * @param String $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param String $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        $roles = $this->roles;

        if ($this->hasRole($role)) {
            $remove[] = $role;
            $this->roles = array_diff($roles, $remove);
        }

        return $this;
    }

    /**
     * @param array $buttons
     *
     * @return $this
     */
    public function setButtons($buttons)
    {
        $this->buttons = $buttons;

        return $this;
    }

    /**
     * @return array $buttons
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @param String $button
     *
     * @return bool
     */
    public function hasButton($button)
    {
        if (in_array($button, $this->buttons)) {
            return true;
        }

        return false;
    }

    /**
     * @param String $button
     *
     * @return $this
     */
    public function addButton($button)
    {
        if (!$this->hasButton($button)) {
            $this->buttons[] = $button;
        }

        return $this;
    }

    /**
     * @param String $button
     *
     * @return $this
     */
    public function removeButton($button)
    {
        $buttons = $this->buttons;

        if ($this->hasButton($button)) {
            $remove[] = $button;
            $this->buttons = array_diff($buttons, $remove);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        if ($this->class) {
            $json['class'] = $this->class;
        }

        $buttons = [];
        foreach ($this->buttons as $button) {
            $buttons[] = $button->jsonSerialize();
        }
        $json['buttons'] = $buttons;

        if (count($this->roles)) {
            $json['roles'] = json_encode($this->roles);
        }

        return $json;
    }
}
