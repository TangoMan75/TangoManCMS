<?php

namespace AppBundle\Entity;

Trait Publishable
{
    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published;

    /**
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }
}