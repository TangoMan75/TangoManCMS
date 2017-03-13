<?php

namespace AppBundle\Entity\Traits;

/**
 * Adds created at and modified at timestamps to entities.
 * Requires that entities are marked with the HasLifecycleCallbacks annotation.
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
     * @ORM\PreUpdate()
     */
    public function updateModified() {
        $this->modified = new \DateTime();
    }

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
}