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
    private $create;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $read;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $update;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $delete;

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
        $this->create = false;
        $this->read = false;
        $this->update = false;
        $this->delete = false;
        $this->readOnly = false;
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
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * @param bool $create
     *
     * @return $this
     */
    public function setCreate($create)
    {
        $this->create = $create;

        return $this;
    }

    /**
     * @return bool
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @param bool $read
     *
     * @return $this
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param bool $update
     *
     * @return $this
     */
    public function setUpdate($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * @return bool
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @param bool $delete
     *
     * @return $this
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * @param $role
     *
     * @return $this
     */
    public function addRole($role)
    {
        if (!in_array($role, $this->roles)) {
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