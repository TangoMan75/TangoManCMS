<?php

namespace AppBundle\Entity;

use TangoMan\RoleBundle\Model\Privilege as TangoManPrivilege;

/**
 * Class Privilege
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrivilegeRepository")
 * @ORM\Table(name="role")
 */
class Privilege extends TangoManPrivilege
{
    use Relationships\PrivilegesHaveRoles;
    use Relationships\PrivilegesHaveUsers;

    public function __construct()
    {
        parent::__construct();
    }
}
