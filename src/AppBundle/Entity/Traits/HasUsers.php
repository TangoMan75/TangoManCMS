<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasUsers
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `User` entity to implement `$items` property with `ManyToMany` and `mappedBy="items"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->users = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasUsers
{
    /**
     * @var array|User[]|ArrayCollection
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
        if (!in_array($user, (array)$this->users)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);

        return $this;
    }
}
