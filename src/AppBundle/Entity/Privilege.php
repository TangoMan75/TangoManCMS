<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Privilege
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="privilege")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrivilegeRepository")
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Privilege
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $canCreate;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $canRead;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $canUpdate;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $canDelete;

    /**
     * @var array|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", mappedBy="privileges")
     */
    private $roles = [];

    /**
     * Privilege constructor.
     */
    public function __construct()
    {
        $this->roless = new ArrayCollection();
        $this->canCreate = false;
        $this->canRead = false;
        $this->canUpdate = false;
        $this->canDelete = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function getCanCreate()
    {
        return $this->canCreate;
    }

    /**
     * @param bool $canCreate
     *
     * @return $this
     */
    public function setCanCreate($canCreate)
    {
        $this->canCreate = $canCreate;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCanRead()
    {
        return $this->canRead;
    }

    /**
     * @param bool $canRead
     *
     * @return $this
     */
    public function setCanRead($canRead)
    {
        $this->canRead = $canRead;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCanUpdate()
    {
        return $this->canUpdate;
    }

    /**
     * @param bool $canUpdate
     *
     * @return $this
     */
    public function setCanUpdate($canUpdate)
    {
        $this->canUpdate = $canUpdate;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCanDelete()
    {
        return $this->canDelete;
    }

    /**
     * @param bool $canDelete
     *
     * @return $this
     */
    public function setCanDelete($canDelete)
    {
        $this->canDelete = $canDelete;

        return $this;
    }

    /**
     * @param $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        if (!in_array($role, (array)$this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param $role
     *
     * @return $this
     */
    public function removeRole($role)
    {
        $this->roles->removeElement($role);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}