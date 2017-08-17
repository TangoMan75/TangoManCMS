<?php

namespace TangoMan\EntityHelper;

/**
 * Trait Featurable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait Featurable
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $featured = false;

    /**
     * @return bool
     */
    public function isFeatured()
    {
        return $this->featured;
    }

    /**
     * @param bool $featured
     */
    public function setFeatured($featured)
    {
        $this->featured = $featured;
    }
}
