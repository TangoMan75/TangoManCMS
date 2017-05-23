<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Item;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait RoleHasUsers
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 1. Requires `User` entity to implement `$roles` property with `ManyToMany` and `mappedBy="roles"` annotation.
 * 2. Requires `User` entity to implement `linkItem` and `unlinkItem` methods.
 * 3. (Optional) entity constructor must initialize ArrayCollection object
 *     $this->users = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
Trait RoleHasUsers
{
    /**
     * @var array|User[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="roles", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
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
     * @return array|User[]|ArrayCollection
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
        if (in_array($user, (array)$this->users)) {
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
        $user->linkItem($this);

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
        $user->unlinkItem($this);

        return $this;
    }

    /**
     * @param User $user
     */
    public function linkUser(User $user)
    {
        if (!in_array($user, (array)$this->users)) {
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
