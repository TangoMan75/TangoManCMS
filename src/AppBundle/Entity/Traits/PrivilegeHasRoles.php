<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Privilege;
use AppBundle\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PrivilegeHasRoles
 * 
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 
 * 1. Requires `Role` entity to implement `$privileges` property with `ManyToMany` and `mappedBy="privileges"` annotation.
 * 2. Requires `Role` entity to implement `linkPrivilege` and `unlinkPrivilege` methods.
 * 3. (Optional) entity constructor must initialize ArrayCollection object
 *     $this->roles = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait PrivilegeHasRoles
{
    /**
     * @var array|Role[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="privileges", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
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
        $this->linkRole($role);
        $role->linkPrivilege($this);

        return $this;
    }

    /**
     * @param Role $role
     *
     * @return $this
     */
    public function removeRole(Role $role)
    {
        $this->unlinkRole($role);
        $role->unlinkPrivilege($this);

        return $this;
    }

    /**
     * @param Role $role
     */
    public function linkRole(Role $role)
    {
        if (!in_array($role, (array)$this->roles)) {
            $this->roles[] = $role;
        }
    }

    /**
     * @param Role $role
     */
    public function unlinkRole(Role $role)
    {
        $this->roles->removeElement($role);
    }
}
