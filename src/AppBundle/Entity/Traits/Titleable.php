<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Titleable
 * Requires that entities owns Sluggable and Timestampable traits.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Titleable
{
    /**
     * @var String
     * @ORM\Column(type="string", nullable=true)
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

    /**
     * Set current date as default title
     * @ORM\PrePersist()
     *
     * @return $this
     */
    public function setDefaultTitle()
    {
        if (!$this->title) {
            $this->setTitle($this->created->format('d/m/Y H:i:s'));
        }

        return $this;
    }
}
