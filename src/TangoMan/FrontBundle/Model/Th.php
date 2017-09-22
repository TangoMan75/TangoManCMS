<?php

namespace TangoMan\FrontBundle\Model;

/**
 * Class Th
 *
 * @package TangoMan\FrontBundle\Model
 */
class Th implements \JsonSerializable
{
    /**
     * Entity name to be used with orderBy
     *
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
     * Hyperlink route
     * e.g: 'app_admin_user_index'
     *
     * @var string
     */
    private $route;

    /**
     * Roles granted privilege to see item
     * (null = no restrictions)
     * e.g: ['ROLE_ADMIN', 'ROLE_SUPER_ADMIN']
     *
     * @var array
     */
    private $roles = [];

    /**
     * OrderBy default way
     * e.g.: 'ASC'
     *
     * @var string
     */
    private $way;

    /**
     * Add colspan attribute to <th> tag
     *
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

        if ($this->route) {
            $json['route'] = $this->route;
        }

        if (count($this->roles)) {
            $json['roles'] = json_encode($this->roles);
        }

        if ($this->way) {
            $json['way'] = $this->way;
        }

        if ($this->colspan) {
            $json['colspan'] = $this->colspan;
        }

        return $json;
    }
}
