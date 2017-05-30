<?php

namespace AppBundle\Entity\Relationships;

// role
use AppBundle\Entity\Role;
// user
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait RolesHaveUsers
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `User` entity to implement `$roles` property with `ManyToMany` and `inversedBy="users"` annotation.
 * 2. Requires `User` entity to implement `linkRole` and `unlinkRole` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->users = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait RolesHaveUsers
{
    /**
     * @var array|User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="roles", cascade={"persist"})
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
        $user->linkRole($this);

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
        $user->unlinkRole($this);

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