<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TangoMan\RoleBundle\Model\Role as TangoManRole;
use TangoMan\RoleBundle\Relationships\RolesHavePrivileges;
use TangoMan\RoleBundle\Relationships\RolesHaveUsers;

/**
 * Class Role
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 * @ORM\Table(name="role")
 */
class Role extends TangoManRole
{
    use RolesHavePrivileges;
    use RolesHaveUsers;

    public function __construct()
    {
        parent::__construct();
    }
}
