<?php

namespace TangoMan\FrontBundle\Model;

/**
 * Class Button
 *
 * @package TangoMan\FrontBundle\Model
 */
class Button implements \JsonSerializable
{
    /**
     * Button type
     * e.g.: 'button', 'dismiss', 'reset', 'submit'
     *
     * @var string
     */
    private $type;

    /**
     * Font icon
     * e.g: 'glyphicon glyphicon-user'
     *
     * @var string
     */
    private $icon;

    /**
     * Label to be displayed
     *
     * @var string
     */
    private $label;

    /**
     * Text to be shown in badge
     *
     * @var string
     */
    private $badge;

    /**
     * Button class
     * e.g.: 'btn btn-warning btn-sm'
     *
     * @var string
     */
    private $class;

    /**
     * Tooltip message to be displayed
     * e.g.: 'Edit this user'
     *
     * @var string
     */
    private $tooltip;

    /**
     * Hyperlink route
     * e.g: 'app_admin_user_index'
     *
     * @var string
     */
    private $route;

    /**
     * Pass callback link with route parameters
     *
     * @var boolean
     */
    private $callback;

    /**
     * Pass target id with route parameters
     *
     * @var integer
     */
    private $id;

    /**
     * Pass target slug with route parameters
     *
     * @var string
     */
    private $slug;

    /**
     * data-toggle attribute
     * e.g: modal
     *
     * @var string
     */
    private $toggle;

    /**
     * data-target attribute
     * e.g: myModal
     *
     * @var string
     */
    private $target;

    /**
     * data-text attribute
     * e.g: myModal
     *
     * @var string
     */
    private $text;

    /**
     * Disabled button
     *
     * @var bool
     */
    private $disabled;

    /**
     * Data attribute
     * e.g.: data-foo="bar"
     *
     * @var array
     */
    private $data = [];

    /**
     * Roles granted privilege to see item
     * (null = no restrictions)
     * e.g: ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']
     *
     * @var array
     */
    private $roles = [];

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
     * @return Button
     */
    public function setType($type)
    {
        $this->type = $type;

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
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @param string $badge
     *
     * @return Button
     */
    public function setBadge($badge)
    {
        $this->badge = $badge;

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
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * @param string $tooltip
     *
     * @return $this
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;

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
     * @return bool
     */
    public function isCallback()
    {
        return $this->callback;
    }

    /**
     * @param bool $callback
     *
     * @return $this
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getToggle()
    {
        return $this->toggle;
    }

    /**
     * @param string $toggle
     *
     * @return $this
     */
    public function setToggle($toggle)
    {
        $this->toggle = $toggle;

        return $this;
    }

    /**
     * @return string
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param string $target
     *
     * @return $this
     */
    public function setTarget($target)
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param bool $disabled
     *
     * @return $this
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;

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
     * @param string $role
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
     * @param string $role
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
     * @param string $role
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
     * @return array
     */
    public function jsonSerialize()
    {
        $json = [];
        if ($this->icon) {
            $json['icon'] = $this->icon;
        }

        if ($this->label) {
            $json['label'] = $this->label;
        }

        if ($this->class) {
            $json['class'] = $this->class;
        }

        if ($this->tooltip) {
            $json['tooltip'] = $this->tooltip;
        }

        if ($this->route) {
            $json['route'] = $this->route;
        }

        if ($this->callback) {
            $json['callback'] = $this->callback;
        }

        if ($this->id) {
            $json['id'] = $this->id;
        }

        if ($this->slug) {
            $json['slug'] = $this->slug;
        }

        if ($this->toggle) {
            $json['toggle'] = $this->toggle;
        }

        if ($this->target) {
            $json['target'] = $this->target;
        }

        if ($this->text) {
            $json['text'] = $this->text;
        }

        if ($this->disabled) {
            $json['disabled'] = $this->disabled;
        }

        if (count($this->data)) {
            $json['data'] = json_encode($this->data);
        }

        if (count($this->roles)) {
            $json['roles'] = json_encode($this->roles);
        }

        return $json;
    }
}
