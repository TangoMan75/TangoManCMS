<?php

namespace AppBundle\Entity;

use TangoMan\RoleBundle\Model\Role as TangoManRole;

/**
 * Class Role
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role extends TangoManRole
{
    use Relationships\RolesHavePrivileges;
    use Relationships\RolesHaveUsers;

    public function __construct()
    {
        parent::__construct();
    }
}
