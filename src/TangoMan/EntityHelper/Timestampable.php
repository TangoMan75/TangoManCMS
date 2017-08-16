<?php

namespace TangoMan\EntityHelper;

/**
 * Trait Timestampable
 * Adds "created" and "modified" timestamps to entity.
 * 1. Requires entity to be marked with "HasLifecycleCallbacks" annotation.
 * 2. Entity constructor must initialize "DateTimeImmutable" object
 *     $this->created  = new \DateTimeImmutable();
 *     $this->modified = new \DateTimeImmutable();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait Timestampable
{
    /**
     * @var \DateTime Creation date
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var \DateTime Date last modified
     * @ORM\Column(type="datetime")
     */
    private $modified;

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     *
     * @return $this
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getModified()
    {
        return $this->modified;
    }

    /**
     * @param \DateTime $modified
     *
     * @return $this
     */
    public function setModified(\DateTime $modified)
    {
        $this->modified = $modified;

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function updateModified()
    {
        $this->modified = new \DateTimeImmutable();
    }
}
