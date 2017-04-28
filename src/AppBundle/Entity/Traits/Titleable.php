<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Titleable
 * Requires entities to own "Sluggable" and "Timestampable" traits.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Titleable
{
    /**
     * @var String
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Slug is generated from title
     *
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
