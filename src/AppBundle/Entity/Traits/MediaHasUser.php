<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Media;
use AppBundle\Entity\User;

/**
 * Trait MediaHasUser
 *
 * This trait defines the INVERSE side of a ManyToOne relationship.
 * 
 * 1. Requires `User` entity to implement `$medias` property with `OneToMany` and `mappedBy="medias"` and cascade={"remove", "persist"} annotation.
 * 2. Requires `User` entity to implement `linkMedia` method.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait MediaHasUser
{
    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="medias", cascade={"persist"})
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
        $user->linkMedia($this);

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
