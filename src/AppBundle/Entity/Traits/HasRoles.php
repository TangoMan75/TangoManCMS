<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasRoles
 * 
 * This trait defines the OWNING side of the relationship.
 * 
 * 1. Role entity must implement inversed property and methods.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->roles = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasRoles
{
    /**
     * @var Role[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", inversedBy="users")
     */
    private $roles = [];

    /**
     * @param Role[]|ArrayCollection $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return Role[]
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
     * @param role $role
     *
     * @return $this
     */
    public function removeRole(role $role)
    {
        $this->roles->removeElement($role);

        return $this;
    }
}
