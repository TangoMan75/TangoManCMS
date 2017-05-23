<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;

/**
 * Trait CommentHasUser
 * This trait defines the INVERSE side of a ManyToOne relationship.
 * 1. Requires `User` entity to implement `$comments` property with `OneToMany` and `mappedBy="comments"` and cascade={"remove", "persist"} annotation.
 * 2. Requires `User` entity to implement `linkComment` method.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait CommentHasUser
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments", cascade={"persist"})
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
        $user->linkComment($this);

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
