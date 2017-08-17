<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UsersHaveRoles
 * This trait defines the OWNING side of a ManyToMany bidirectional relationship.
 * 1. Requires owned `Role` entity to implement `$users` property with `ManyToMany` and `mappedBy="roles"` annotation.
 * 2. Requires owned `Role` entity to implement `linkUser` and `unlinkUser` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->roles = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait UsersHaveRoles
{
    /**
     * @var array|Role[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     * @ORM\OrderBy({"id"="DESC"})
     */
    protected $roles = [];

    /**
     * @param array|Role[]|ArrayCollection $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        foreach (array_diff($this->roles, $roles) as $role) {
            $this->unlinkRole($role);
        }

        foreach ($roles as $role) {
            $this->linkRole($role);
        }

        return $this;
    }

    /**
     * @return array $roles
     */
    public function getRoles()
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->getType();
        }

        // Every user has "ROLE_USER"
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }

        return $roles;
    }

    /**
     * @return Role[] $roles
     */
    public function getListRoles()
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
        if ($role->getType() == 'ROLE_USER') {
            return true;
        }

        if ($this->roles->contains($role)) {
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
        $role->linkUser($this);

        return $this;
    }

    /**
     * @param Role|String $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        $this->unlinkRole($role);

        if ($role !== null) {
            $role->unlinkUser($this);
        }

        return $this;
    }

    /**
     * @param Role $role
     */
    public function linkRole(Role $role)
    {
        if (!$this->roles->contains($role)) {
            $this->roles[] = $role;
        }
    }

    /**
     * @param Role|String $role
     */
    public function unlinkRole(&$role)
    {
        $role = $this->checkRole($role);
        $this->roles->removeElement($role);
    }

    /**
     * @param Role|String $role
     *
     * @return Role|null
     */
    private function checkRole($role)
    {
        if ($role instanceof Role) {
            return $role;
        }

        foreach ($this->roles as $temp) {
            if ($temp->getType() == $role) {
                return $temp;
            }
        }

        return null;
    }
}
