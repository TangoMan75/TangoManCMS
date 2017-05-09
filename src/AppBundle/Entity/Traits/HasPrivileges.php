<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Privilege;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasPrivileges
 *
 * This trait defines the OWNING side of a ManyToMany relationship.
 *
 * 1. Requires owned `Privilege` entity to implement `$owners` property with `ManyToMany` and `inversedBy="privileges"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->privileges = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasPrivileges
{
    /**
     * @var array|Privilege[]|ArrayCollection
     */
    private $privileges = [];

    /**
     * @param array|Privilege[]|ArrayCollection $privileges
     *
     * @return $this
     */
    public function setPrivileges($privileges)
    {
        $this->privileges = $privileges;

        return $this;
    }

    /**
     * @return array|Privilege[]|ArrayCollection $privileges
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
     * @param Privilege $privilege
     *
     * @return $this
     */
    public function removePrivilege(Privilege $privilege)
    {
        $this->privileges->removeElement($privilege);

        return $this;
    }
}
