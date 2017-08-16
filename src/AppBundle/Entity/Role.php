<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Role\RoleInterface;

use TangoMan\UserBundle\Model\Role as TangoManRole;

/**
 * Class Role
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="role")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Role extends TangoManRole
{
    /**
     * Role constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
