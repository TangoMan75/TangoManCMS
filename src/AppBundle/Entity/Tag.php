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
     * @Assert\NotBlank(message="L'étiquette doit appartenir à un type.")
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @var array|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="tags")
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="tags")
     */
    private $items = [];

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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $type
     *
     * @return Tag
     */
    public function setType($type)
    {
        if (!$this->readOnly) {
            $slug = trim($type);
            // Remove accents
            $slug = htmlentities($slug, ENT_NOQUOTES, 'UTF-8');
            $slug = preg_replace('/&#?([a-zA-Z])[a-zA-Z0-9]*;/i', '${1}', $slug);
            // Convert string
            $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
            // Remove illegal characters
            $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
            $slug = mb_strtolower(trim($slug, '-'), 'UTF-8');
            $slug = preg_replace("/[\/_|+ -]+/", '-', $slug);
            $this->type = $slug;
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
            $this->label = mb_strtolower($label, 'UTF-8');
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
     * @return ArrayCollection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
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