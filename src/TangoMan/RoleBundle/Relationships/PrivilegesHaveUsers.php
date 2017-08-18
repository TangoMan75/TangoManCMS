<?php

namespace TangoMan\RoleBundle\Relationships;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entities\User;

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
 * @package TangoMan\RoleBundle\Relationships
 */
trait PrivilegesHaveUsers
{
    /**
     * @var array|User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entities\User", mappedBy="privileges", cascade={"persist"})
     */
    protected $users = [];

    /**
     * @param array|User[]|ArrayCollection $users
     *
     * @return $this
     */
    public function setUsers($users)
    {
        foreach (array_diff($this->users, $users) as $user) {
            $this->unlinkUser($user);
        }

        foreach ($users as $user) {
            $this->linkUser($user);
        }

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
