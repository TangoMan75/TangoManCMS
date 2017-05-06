<?php

namespace AppBundle\Entity\Traits;

/**
 * Class HasRoles
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait HasRoles
{
    /**
     * @var array User roles
     * @ORM\Column(type="simple_array")
     */
    private $roles = [];

    /**
     * @return Role[]|array
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
    public function hasRole($role)
    {
        if (in_array($role, $this->roles)) {
            return true;
        }

        return false;
    }

    /**
     * @param Role $role
     */
    public function addRole($role)
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
    public function removeRole($role)
    {
        $this->roles->removeElement($role);

        return $this;
    }
}
