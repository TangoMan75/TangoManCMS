<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use TangoMan\RoleBundle\Model\Privilege as TangoManPrivilege;
use TangoMan\RoleBundle\Relationships\PrivilegesHaveRoles;
use TangoMan\RoleBundle\Relationships\PrivilegesHaveUsers;

/**
 * Class Privilege
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrivilegeRepository")
 * @ORM\Table(name="privilege")
 */
class Privilege extends TangoManPrivilege
{
    use PrivilegesHaveRoles;
    use PrivilegesHaveUsers;

    public function __construct()
    {
        parent::__construct();
    }
}
