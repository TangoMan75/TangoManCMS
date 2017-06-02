<?php

namespace AppBundle\Entity\Relationships;

// post
use AppBundle\Entity\Post;
// user
use AppBundle\Entity\User;

/**
 * Trait PostsHaveUser
 * This trait defines the OWNING side of a ManyToOne relationship.
 * 1. Requires `User` entity to implement `$posts` property with `OneToMany` and `mappedBy="posts"` annotation.
 * 2. Requires `User` entity to implement linkPost(Post $post) public method.
 * 3. Requires `User` entity to have `cascade={"remove"}` to avoid orphan objects on `User` deletion.
 * 4. `cascade={"persist"}` on this side on the relationship is fine (applies to one `User` only).
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait PostsHaveUser
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="posts", cascade={"persist"})
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
        if ($user) {
            $this->linkUser($user);
            $user->linkPost($this);
        } else {
            $this->unlinkUser();
            $user->unlinkPost($this);
        }

        return $this;
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
