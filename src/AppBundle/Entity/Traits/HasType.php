<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasType
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasType
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }
}
