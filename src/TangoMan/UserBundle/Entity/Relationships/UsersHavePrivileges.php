<?php

namespace TangoMan\UserBundle\Entity\Relationships;

use TangoMan\UserBundle\Entity\Privilege;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UsersHavePrivileges
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 1. Requires owned `Privilege` entity to implement `$users` property with `ManyToMany` and `mappedBy="privileges"` annotation.
 * 2. Requires owned `Privilege` entity to implement `linkUser` and `unlinkUser` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->privileges = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\UserBundle\Entity\Relationships
 */
trait UsersHavePrivileges
{
    /**
     * @var array|Privilege[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="TangoMan\UserBundle\Entity\Privilege", inversedBy="users", cascade={"persist"})
     */
    private $privileges = [];

    /**
     * @param array|Privilege[]|ArrayCollection $privileges
     *
     * @return $this
     */
    public function setPrivileges($privileges)
    {
        foreach (array_diff($this->privileges, $privileges) as $privilege) {
            $this->unlinkPrivilege($privilege);
        }

        foreach ($privileges as $privilege) {
            $this->linkPrivilege($privilege);
        }

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
     * @return array $privileges
     */
    public function getPrivilegesAsArray()
    {
        $privileges = [];
        foreach ($this->privileges as $privilege) {
            $privileges[] = $privilege->getType();
        }

        return $privileges;
    }

    /**
     * @param Privilege $privilege
     *
     * @return bool
     */
    public function hasPrivilege(Privilege $privilege)
    {
        if ($this->privileges->contains($privilege)) {
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
        $privilege->linkUser($this);

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
        $privilege->unlinkUser($this);

        return $this;
    }

    /**
     * @param Privilege $privilege
     */
    public function linkPrivilege(Privilege $privilege)
    {
        if (!$this->privileges->contains($privilege)) {
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
