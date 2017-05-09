<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Role
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Role
{
    use Traits\HasType;
    use Traits\HasName;
    use Traits\HasPrivileges;
    use Traits\HasUsers;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Privilege[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Privilege", inversedBy="roles", cascade={"persist"})
     * @ORM\OrderBy({"name"="DESC"})
     */
    private $privileges = [];

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="roles", cascade={"persist"})
     * @ORM\OrderBy({"username"="DESC"})
     */
    private $users = [];

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->privileges = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }

}
