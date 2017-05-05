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
    use Traits\Slugify;

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
     * @Assert\NotBlank(message="L'étiquette doit avoir un nom.")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="boolean")
     */
    private $create;

    /**
     * @var string
     * @ORM\Column(type="boolean")
     */
    private $read;

    /**
     * @var string
     * @ORM\Column(type="boolean")
     */
    private $update;

    /**
     * @var string
     * @ORM\Column(type="boolean")
     */
    private $delete;

    /**
     * @var array|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Role", mappedBy="privileges")
     */
    private $role = [];

    /**
     * Privilege constructor.
     */
    public function __construct()
    {
        $this->role = new ArrayCollection();
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
     * @return Privilege
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
     * @return string
     */
    public function getCreate()
    {
        return $this->create;
    }

    /**
     * @param string $create
     *
     * @return Privilege
     */
    public function setCreate($create)
    {
        $this->create = $create;

        return $this;
    }

    /**
     * @return string
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * @param string $read
     *
     * @return Privilege
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @return string
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * @param string $update
     *
     * @return Privilege
     */
    public function setUpdate($update)
    {
        $this->update = $update;

        return $this;
    }

    /**
     * @return string
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * @param string $delete
     *
     * @return Privilege
     */
    public function setDelete($delete)
    {
        $this->delete = $delete;

        return $this;
    }

    /**
     * @return Privilege
     */
    public function addItem($item)
    {
        if (!in_array($item, $this->role)) {
            $this->role[] = $item;
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->role;
    }

    /**
     * @param $item
     *
     * @return Privilege
     */
    public function removeItem($item)
    {
        $this->role->removeElement($item);

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