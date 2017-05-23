<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Privilege;
use AppBundle\Entity\Role;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait RoleHasPrivileges
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 1. Requires `Privilege` entity to implement `$roles` property with `ManyToMany` and `inversedBy="privileges"` annotation.
 * 2. Requires `Privilege` entity to implement `linkRole` and `unlinkRole` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->privileges = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
Trait RoleHasPrivileges
{
    /**
     * @var array|Privilege[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Privilege", mappedBy="roles", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
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
        $this->linkPrivilege($privilege);
        $privilege->linkRole($this);

        return $this;
    }

    /**
     * @param Privilege $privilege
     *
     * @return $this
     */
    public function removePrivilege(Privilege $privilege)
    {
        $this->unlinkPrivilege($privilege);
        $privilege->unlinkRole($this);

        return $this;
    }

    /**
     * @param Privilege $privilege
     */
    public function linkPrivilege(Privilege $privilege)
    {
        if (!in_array($privilege, (array)$this->privileges)) {
            $this->privileges[] = $privilege;
        }
    }

    /**
     * @param Privilege $privilege
     */
    public function unlinkPrivilege(Privilege $privilege)
    {
        $this->privileges->removeElement($privilege);
    }
}
