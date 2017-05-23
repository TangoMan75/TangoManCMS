<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\User;
use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Entity\Stat;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait StatHasLeads
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 1. Requires owned `Lead` entity to implement `$leads` property with `ManyToMany` and `inversedBy="leads"` annotation.
 * 2. Requires owned `Lead` entity to implement `linkStat` and `unlinkStat` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->leads = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait StatHasLeads
{
    /**
     * @var User[]|Post[]|Page[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="leads", cascade={"persist"})
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="leads", cascade={"persist"})
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Page", mappedBy="leads", cascade={"persist"})
     */
    private $leads = [];

    /**
     * @param array|ArrayCollection $leads
     *
     * @return $this|Stat
     */
    public function setLeads($leads)
    {
        $this->leads = $leads;

        return $this;
    }

    /**
     * @return array|ArrayCollection $leads
     */
    public function getLeads()
    {
        return $this->leads;
    }

    /**
     * @return bool
     */
    public function hasLead($lead)
    {
        if (in_array($lead, (array)$this->leads)) {
            return true;
        }

        return false;
    }

    /**
     * @return $this|Stat
     */
    public function addLead($lead)
    {
        $this->linkLead($lead);
        $lead->linkStat($this);

        return $this;
    }

    /**
     * @return $this|Stat
     */
    public function removeLead($lead)
    {
        $this->unlinkLead($lead);
        $lead->unlinkStat($this);

        return $this;
    }

    public function linkLead($lead)
    {
        if (!in_array($lead, (array)$this->leads)) {
            $this->leads[] = $lead;
        }
    }

    public function unlinkLead($lead)
    {
        $this->leads->removeElement($lead);
    }
}
