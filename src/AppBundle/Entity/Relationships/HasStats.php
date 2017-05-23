<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Stat;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasStat
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 1. Requires `Stat` entity to implement `$leads` property with `ManyToMany` and `mappedBy="leads"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->stats = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait HasStat
{
    /**
     * @var Stat[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Stat", inversedBy="leads", cascade={"persist"})
     * @ORM\OrderBy({"name"="DESC"})
     */
    private $stats = [];

    /**
     * @param Stat[]|ArrayCollection $stats
     *
     * @return $this
     */
    public function setStat($stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * @return array|Stat[]|ArrayCollection
     */
    public function getStat()
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
        $stat->linkItem($this);

        return $this;
    }

    /**
     * @param Stat $stat
     *
     * @return $this
     */
    public function removeStat(Stat $stat)
    {
        $this->unlinkStat($stat);
        $stat->unlinkItem($this);

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
     */
    public function unlinkStat(Stat $stat)
    {
        $this->stats->removeElement($stat);
    }
}
