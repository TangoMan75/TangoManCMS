<?php

namespace TangoMan\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Privilege
 * @ORM\Entity(repositoryClass="TangoMan\UserBundle\Repository\PrivilegeRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="privilege")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\UserBundle\Entity
 */
class Privilege
{
    use Relationships\PrivilegesHaveRoles;
    use Relationships\PrivilegesHaveUsers;

    use Traits\HasLabel;
    use Traits\HasName;
    use Traits\HasType;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Privilege constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setDefaults()
    {
        // Default privilege type is uppercased name with "CAN_" prefix without whitespaces
        if (!$this->type) {
            $this->type = $this->name;
        }

        $this->type = strtoupper(str_replace(' ', '_', $this->type));

        if (stripos($this->type, 'CAN_') !== 0) {
            $this->type = 'CAN_'.$this->type;
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
}