<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tag
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 */
class Tag
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
     * @Assert\NotBlank(message="L'étiquette doit avoir un nom.")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @var Post[]|Page[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="tags")
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="tags")
     */
    private $items;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $readOnly;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * @return Tag
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
     * Set type
     *
     * @param string $type
     *
     * @return Tag
     */
    public function setType($type)
    {
        if (!$this->readOnly) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Add item
     *
     * @return Tag
     */
    public function addItem($item)
    {
        if (!in_array($item, $this->items)) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * Get items
     *
     * @return Page[]|Post[]
     */
    public function getItems()
    {
        return $this->items;
    }


    /**
     * Remove item
     *
     * @param $item
     *
     * @return Tag
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
     * @return Tag
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
