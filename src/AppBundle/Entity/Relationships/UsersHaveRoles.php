<?php

namespace AppBundle\Entity\Relationships;

// role
use AppBundle\Entity\Role;
// user
use AppBundle\Entity\User;
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
     * @return array $roles
     */
    public function getRoles()
    {
        return $this->getRolesAsArray();
    }

    /**
     * @return array $roles
     */
    public function getRolesAsArray()
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles[] = $role->getType();
        }
        return $roles;
    }

    /**
     * @param Role $role
     *
     * @return bool
     */
    public function hasRole(Role $role)
    {
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
     * @param Role $role
     *
     * @return $this
     */
    public function removeRole(Role $role)
    {
        $this->unlinkRole($role);
        $role->unlinkUser($this);

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
     * @param Role $role
     */
    public function unlinkRole(Role $role)
    {
        $this->roles->removeElement($role);
    }
}
