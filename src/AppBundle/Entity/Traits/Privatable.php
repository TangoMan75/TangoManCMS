<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait Privatable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait Privatable
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $private = false;

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * @param bool $private
     */
    public function setPrivate($private)
    {
        $this->private = $private;
    }
}
