<?php

namespace TangoMan\EntityHelper;

/**
 * Trait Privatable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait Privatable
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $private = true;

    /**
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return !$this->private;
    }

    /**
     * @return $this
     */
    public function setPrivate()
    {
        $this->private = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function setPublic()
    {
        $this->private = false;

        return $this;
    }
}
