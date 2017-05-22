<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Stats;

/**
 * Trait HasStats
 * This trait defines the OWNING side of a OneToOne relationship.
 * OneToOne does not require any implementation on the owned side of the relationship.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasStats
{
    /**
     * @var Stats
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Stats", fetch="EAGER")
     */
    private $stats;

    /**
     * @param Stats $stats
     *
     * @return $this
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * @return Stats $stats
     */
    public function getStats()
    {
        return $this->stats;
    }
}
