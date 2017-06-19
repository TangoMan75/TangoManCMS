<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Role
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="role")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Role
{
    use Relationships\RolesHavePrivileges;
    use Relationships\RolesHaveUsers;

    use Traits\HasLabel;
    use Traits\HasName;
    use Traits\HasType;
    use Traits\Privatable;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

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
    public function getIcon()
    {
        if (!$this->icon) {
            return 'glyphicon glyphicon-user';
        } else {
            return $this->icon;
        }
    }

    /**
     * @param string $icon
     *
     * @return Role
     */
    public function setIcon($icon)
    {
        if (in_array(
            $icon,
            [
                'glyphicon glyphicon-user',
                'glyphicon glyphicon-pawn',
                'glyphicon glyphicon-knight',
                'glyphicon glyphicon-bishop',
                'glyphicon glyphicon-tower',
                'glyphicon glyphicon-queen',
                'glyphicon glyphicon-king',
            ]
        )) {
            $this->icon = $icon;
        } else {
            $this->icon = 'glyphicon glyphicon-user';
        }

        return $this;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setDefaults()
    {
        if (!$this->type) {
            $this->type = $this->name;
        }

        $this->type = strtoupper(str_replace(' ', '_', $this->type));

        if (stripos($this->type, 'ROLE_') !== 0) {
            $this->type = 'ROLE_'.$this->type;
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
