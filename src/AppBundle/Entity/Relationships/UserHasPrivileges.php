<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Privilege;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UserHasPrivileges
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 1. Requires `Privilege` entity to implement `$users` property with `ManyToMany` and `inversedBy="privileges"` annotation.
 * 2. Requires `Privilege` entity to implement `linkUser` and `unlinkUser` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->privileges = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait UserHasPrivileges
{
    /**
     * @var array|Privilege[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Privilege", mappedBy="users", cascade={"persist"})
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
