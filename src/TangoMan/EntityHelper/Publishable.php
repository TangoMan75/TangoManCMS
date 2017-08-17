<?php

namespace TangoMan\EntityHelper;

/**
 * Trait Publishable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait Publishable
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $published = false;

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     *
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }
}
