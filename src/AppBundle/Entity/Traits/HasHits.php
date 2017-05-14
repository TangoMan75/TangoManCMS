<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait HasHits
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasHits
{
    /**
     * @var
     * @ORM\Column(type="integer", nullable=true)
     */
    private $hits;

    /**
     * @return integer
     */
    public function getHits()
    {
        return $this->hits;
    }

    /**
     * @param integer $hits
     * @return $this
     */
    public function setHits($hits)
    {
        $this->hits = $hits;

        return $this;
    }

    /**
     * @return $this
     */
    public function addHit()
    {
        $this->hits = ++$this->hits;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasHit()
    {
        if ($this->hits) {
            return true;
        }

        return false;
    }
}
