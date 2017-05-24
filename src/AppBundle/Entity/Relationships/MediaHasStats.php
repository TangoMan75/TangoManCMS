<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Stat;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait MediaHasStats
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 
 * 1. Requires `Stat` entity to implement `$media` property with `ManyToOne` and `inversedBy="stats"` annotation.
 * 2. Requires `Stat` entity to implement linkMedia(Media $media) public method.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->stats = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait MediaHasStats
{
    /**
     * @var array|Stat[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Stat", mappedBy="media", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $stats = [];

    /**
     * @param array|Stat[]|ArrayCollection $stats
     *
     * @return $this
     */
    public function setStats($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * @return array|Stat[]|ArrayCollection $stats
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param Stat $stat
     *
     * @return bool
     */
    public function hasStat(Stat $stat)
    {
        if (in_array($stat, (array)$this->stats)) {
            return true;
        }

        return false;
    }

    /**
     * @param Stat $stat
     *
     * @return $this
     */
    public function addStat(Stat $stat)
    {
        $this->linkStat($stat);
        $stat->linkMedia($this);

        return $this;
    }

    /**
     * @param Stat $stat
     */
    public function linkStat(Stat $stat)
    {
        if (!in_array($stat, (array)$this->stats)) {
            $this->stats[] = $stat;
        }
    }

    /**
     * @param Stat $stat
     *
     * @return $this
     */
    public function removeStat(Stat $stat)
    {
        $this->unlinkStat($stat);
        $stat->unlinkMedia($this);

        return $this;
    }

    /**
     * @param Stat $stat
     */
    public function unlinkStat(Stat $stat)
    {
        $this->stats->removeElement($stat);
    }
}
