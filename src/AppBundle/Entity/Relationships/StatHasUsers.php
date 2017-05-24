<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait StatHasUsers
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 
 * 1. Requires `User` entity to implement `$stat` property with `ManyToOne` and `inversedBy="users"` annotation.
 * 2. Requires `User` entity to implement linkStat(Stat $stat) public method.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->users = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait StatHasUsers
{
    /**
     * @var array|User[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="stat", cascade={"persist"})
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
        $user->linkStat($this);

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
     *
     * @return $this
     */
    public function removeUser(User $user)
    {
        $this->unlinkUser($user);
        $user->unlinkStat($this);

        return $this;
    }

    /**
     * @param User $user
     */
    public function unlinkUser(User $user)
    {
        $this->users->removeElement($user);
    }
}
