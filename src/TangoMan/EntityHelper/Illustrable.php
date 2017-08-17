<?php

namespace TangoMan\EntityHelper;

/**
 * Trait Illustrable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait Illustrable
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $image;

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }
}
