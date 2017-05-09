<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\User;

/**
 * Trait HasUser
 *
 * This trait defines the INVERSE side of a ManyToOne relationship.
 *
 * Requires `User` entity to implement `$items` property with `OneToMany` and `mappedBy="items"` annotation.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasUser
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
