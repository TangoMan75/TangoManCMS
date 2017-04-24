<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Coverable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Coverable
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $cover;

    /**
     * @return string
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param string $cover
     *
     * @return $this
     */
    public function setCover($cover)
    {
        $this->cover = $cover;

        return $this;
    }
}
