<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasTypes
 * This class is designed to provide a simple and straitforward way to categorize entities.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasTypes
{
    /**
     * @var array|ArrayCollection
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $types = [];

    /**
     * @param array $type
     *
     * @return $this
     */
    public function setTypes($types)
    {
        $this->types = $types;

        return $this;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return bool
     */
    public function hasType($type)
    {
        if (in_array($type, (array)$this->types)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function addType($type)
    {
        if (!in_array($type, (array)$this->types)) {
            $this->types[] = $type;
        }

        return $this;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function removeType($type)
    {
        $this->types->removeElement($type);

        return $this;
    }
}
