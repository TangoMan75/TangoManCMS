<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;

/**
 * Trait PostHasUser
 * This trait defines the INVERSE side of a ManyToOne relationship.
 * 1. Requires `User` entity to implement `$posts` property with `OneToMany` and `mappedBy="posts"` and cascade={"remove", "persist"} annotation.
 * 2. Requires `User` entity to implement `linkPost` method.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
Trait PostHasUser
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts", cascade={"persist"})
     * @ORM\OrderBy({"username"="DESC"})
     */
    private $user;

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->linkUser($user);
        $user->linkPost($this);

        return $this;
    }

    /**
     * @param User $user
     */
    public function linkUser(User $user)
    {
        $this->user = $user;
    }
}
