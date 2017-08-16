<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TangoMan\UserBundle\Model\Privilege as TangoManPrivilege;

/**
 * Class Privilege
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrivilegeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="privilege")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Privilege extends TangoManPrivilege
{
    /**
     * Privilege constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
}
