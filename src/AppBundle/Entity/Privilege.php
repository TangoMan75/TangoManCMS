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
    use Traits\HasName;
    use Traits\HasRoles;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var array|Role[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", mappedBy="privileges", cascade={"persist"})
     * @ORM\OrderBy({"name"="DESC"})
     */
    private $roles = [];

    /**
     * Privilege constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
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
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}