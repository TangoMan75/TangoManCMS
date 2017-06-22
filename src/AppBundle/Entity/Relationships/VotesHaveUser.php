<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\User;

/**
 * Trait VotesHaveUser
 * This trait defines the OWNING side of a ManyToOne relationship.
 * 1. Requires `User` entity to implement `$votes` property with `OneToMany` and `mappedBy="votes"` annotation.
 * 2. Requires `User` entity to implement linkVote(Vote $vote) public method.
 * 3. Requires `User` entity to have `cascade={"remove"}` to avoid orphan objects on `User` deletion.
 * 4. `cascade={"persist"}` on this side on the relationship is fine (applies to one `User` only).
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait VotesHaveUser
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="votes", cascade={"persist"})
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
            $user->linkVote($this);
        } else {
            $this->unlinkUser();
            $user->unlinkVote($this);
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
