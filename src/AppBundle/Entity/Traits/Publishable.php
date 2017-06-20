<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait Publishable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait Publishable
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published = false;

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     *
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }
}
