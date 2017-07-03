<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\User;

/**
 * Trait CommentsHaveUser
 * This trait defines the OWNING side of a ManyToOne relationship.
 * 1. Requires `User` entity to implement `$comments` property with `OneToMany` and `mappedBy="comments"` annotation.
 * 2. Requires `User` entity to implement linkComment(Comment $comment) public method.
 * 3. Requires `User` entity to have `cascade={"remove"}` to avoid orphan objects on `User` deletion.
 * 4. `cascade={"persist"}` on this side on the relationship is fine (applies to one `User` only).
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait CommentsHaveUser
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments", cascade={"persist"})
     */
    private $user;

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        if ($user) {
            $this->linkUser($user);
            $user->linkComment($this);
        } else {
            $this->unlinkUser();
            $user->unlinkComment($this);
        }

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function linkUser(User $user)
    {
        $this->user = $user;
    }

    public function unLinkUser()
    {
        $this->user = null;
    }
}
