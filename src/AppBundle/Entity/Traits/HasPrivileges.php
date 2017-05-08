<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Privilege;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasPrivileges
 * 
 * This trait defines the OWNING side of the relationship.
 * 
 * 1. Privilege entity must implement inversed property and methods.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->privileges = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasPrivileges
{
    /**
     * @var Privilege[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Privilege", inversedBy="privileged")
     */
    private $privileges = [];

    /**
     * @param Privilege[]|ArrayCollection $privileges
     *
     * @return $this
     */
    public function setPrivileges($privileges)
    {
        $this->privileges = $privileges;

        return $this;
    }

    /**
     * @return Privilege[]
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }

    /**
     * @param Privilege $privilege
     *
     * @return bool
     */
    public function hasPrivilege(Privilege $privilege)
    {
        if (in_array($privilege, (array)$this->privileges)) {
            return true;
        }

        return false;
    }

    /**
     * @param Privilege $privilege
     *
     * @return $this
     */
    public function addPrivilege(Privilege $privilege)
    {
        if (!in_array($privilege, (array)$this->privileges)) {
            $this->privileges[] = $privilege;
        }

        return $this;
    }

    /**
     * @param privilege $privilege
     *
     * @return $this
     */
    public function removePrivilege(privilege $privilege)
    {
        $this->privileges->removeElement($privilege);

        return $this;
    }
}
