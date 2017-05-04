<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Privilege
 * @ORM\Table(name="privilege")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PrivilegeRepository")
 * @ORM\HasLifecycleCallbacks
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
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @var array|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Media", mappedBy="privileges")
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="privileges")
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="privileges")
     */
    private $items = [];

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $readOnly;

    /**
     * Privilege constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->label = 'default';
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
        if (!$this->readOnly) {
            $this->name = $name;
        }

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
     * @param string $type
     *
     * @return Privilege
     */
    public function setType($type)
    {
        if (!$this->readOnly) {
            $this->type = $this->slugify($type);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        if (!$this->readOnly) {
            $this->label = $this->slugify($label);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return Privilege
     */
    public function addItem($item)
    {
        if (!in_array($item, $this->items)) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $item
     *
     * @return Privilege
     */
    public function removeItem($item)
    {
        if (!$this->readOnly) {
            $this->items->removeElement($item);
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
     * Set default values
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     *
     * @return $this
     */
    public function setDefaults()
    {
        if (!$this->type) {
            $this->setType($this->name);
        }

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