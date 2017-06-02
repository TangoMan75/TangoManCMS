<?php

namespace AppBundle\Entity\Relationships;

// privilege
use AppBundle\Entity\Privilege;
// user
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PrivilegesHaveUsers
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 1. Requires `User` entity to implement `$privileges` property with `ManyToMany` and `inversedBy="users"` annotation.
 * 2. Requires `User` entity to implement `linkPrivilege` and `unlinkPrivilege` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->users = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait PrivilegesHaveUsers
{
    /**
     * @var array|User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="privileges", cascade={"persist"})
     */
    private $users = [];

    /**
     * @param array|User[]|ArrayCollection $users
     *
     * @return $this
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return array|User[]|ArrayCollection $users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user)
    {
        if ($this->users->contains($user)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addUser(User $user)
    {
        $this->linkUser($user);
        $user->linkPrivilege($this);

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function removeUser(User $user)
    {
        $this->unlinkUser($user);
        $user->unlinkPrivilege($this);

        return $this;
    }

    /**
     * @param User $user
     */
    public function linkUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
    }

    /**
     * @param User $user
     */
    public function unlinkUser(User $user)
    {
        $this->users->removeElement($user);
    }
}
