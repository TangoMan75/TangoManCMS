<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Featurable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Featurable
{
    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $featured;

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
