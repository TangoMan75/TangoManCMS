<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Stat;

/**
 * Trait HasStat
 *
 * This trait defines the OWNING side of a ManyToOne relationship.
 *
 * 1. Requires `Stat` entity to implement `$leads` property with `OneToMany` and `mappedBy="leads"` annotation.
 * 2. Requires `Stat` entity to implement linkLead(Lead $lead) public method.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait HasStat
{
    /**
     * @var Stat
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Stat", inversedBy="leads", cascade={"persist"})
     */
    private $stat;

    /**
     * @return Stat
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * @param Stat $stat
     *
     * @return $this
     */
    public function setStat(Stat $stat)
    {
        $this->linkStat($stat);
        $stat->linkLead($this);

        return $this;
    }

    /**
     * @param Stat $stat
     */
    public function linkStat(Stat $stat)
    {
        $this->stat = $stat;
    }
}
