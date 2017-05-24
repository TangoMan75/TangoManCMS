<?php

namespace AppBundle\Entity\Relationships;

// media
use AppBundle\Entity\Media;
// user
use AppBundle\Entity\User;

/**
 * Trait MediasHaveUser
 *
 * This trait defines the OWNING side of a ManyToOne relationship.
 *
 * 1. Requires `User` entity to implement `$medias` property with `OneToMany` and `mappedBy="medias"` annotation.
 * 2. Requires `User` entity to implement linkMedia(Media $media) public method.
 * 3. Requires `User` entity to have `cascade={"remove"}` to avoid orphan objects on `User` deletion.
 * 4. `cascade={"persist"}` on this side on the relationship is fine (applies to one `User` only).
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait MediasHaveUser
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="medias", cascade={"persist"})
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
            $user->linkMedia($this);
        } else {
            $this->unlinkUser();
            $user->unlinkMedia($this);
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
