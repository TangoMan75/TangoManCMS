<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\User;

/**
 * Trait HasSimpleRoles
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait HasSimpleRoles
{
    /**
     * @var array User roles
     * @ORM\Column(type="simple_array")
     */
    private $roles = [];

    /**
     * Get roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set roles.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        foreach ($roles as $role) {
            if (!in_array($role, $this->roles)) {
                array_push($this->roles, $role);
            }
        }

        $this->roles = $roles;

        return $this;
    }

    /**
     * Add role.
     *
     * @param string $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $hierarchy = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_SUPER_USER', 'ROLE_USER'];

        foreach ($hierarchy as $key => $value) {
            if ($role == $value) {
                for ($i = $key; $i < count($hierarchy); $i++) {
                    if (!in_array($hierarchy[$i], $this->roles)) {
                        array_push($this->roles, $hierarchy[$i]);
                    }
                }

                return $this;
            }
        }

        if (!in_array($role, $this->roles)) {
            array_push($this->roles, $role);
        }

        return $this;
    }

    /**
     * Remove role.
     *
     * @param string $role
     *
     * @return User
     */
    public function removeRole($role)
    {
        $roles = $this->roles;
        if (in_array($role, $roles)) {
            $remove[] = $role;
            $this->roles = array_diff($roles, $remove);
        }

        return $this;
    }
}
