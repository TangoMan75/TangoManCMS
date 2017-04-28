<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Role
 * @ORM\Table(name="role")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoleRepository")
 */
class Role
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
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $role;

    /**
     * @var User[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="roles")
     */
    private $users;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $hierarchy;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $readOnly;

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->hierarchy = 0;
        $this->readOnly = false;
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
     * Set name
     *
     * @param string $name
     *
     * @return Role
     */
    public function setName($name)
    {
        if (!$this->readOnly) {
            $this->name = $name;
        }

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @param string $role
     *
     * @return $this
     */
    public function setRole($role)
    {
        if (!$this->readOnly) {
            $this->role = $role;
        }

        return $this;
    }

    /**
     * Get users
     *
     * @return Post[]|array
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set users
     *
     * @return Role
     */
    public function addUser($user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param $user
     *
     * @return Role
     */
    public function removeUser($user)
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHierarchy()
    {
        return $this->hierarchy;
    }

    /**
     * @param $hierarchy
     *
     * @return $this
     */
    public function setHierarchy($hierarchy)
    {
        if (!$this->readOnly) {
            $this->hierarchy = $hierarchy;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isReadOnly()
    {
        return $this->readOnly;
    }

    /**
     * @return $this
     */
    public function setReadOnly()
    {
        $this->readOnly = true;

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
