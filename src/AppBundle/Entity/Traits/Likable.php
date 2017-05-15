<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait Likable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Likable
{
    /**
     * @var
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likes;

    /**
     * @return integer
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param integer $likes
     * @return $this
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * @return $this
     */
    public function addLike()
    {
        $this->likes = ++$this->likes;

        return $this;
    }
}
