<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasRoles
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Role` entity to implement `$items` property with `ManyToMany` and `mappedBy="items"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->roles = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasRoles
{
    /**
     * @var array|Role[]|ArrayCollection
     */
    private $roles = [];

    /**
     * @param array|Role[]|ArrayCollection $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return array|Role[]|ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function hasRole(Role $role)
    {
        if (in_array($role, (array)$this->roles)) {
            return true;
        }

        return false;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function addRole(Role $role)
    {
        if (!in_array($role, (array)$this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeRole(Role $role)
    {
        $this->roles->removeElement($role);

        return $this;
    }
}
