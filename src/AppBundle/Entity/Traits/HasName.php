<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait HasName
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasName
{
    /**
     * @var String
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
}
