<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Event
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="log")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Log
{
    use Traits\HasSummary;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime Event Timestamp
     * @ORM\Column(type="datetime")
     */
    private $timestamp;

    /**
     * Event constructor.
     */
    public function __construct()
    {
        $this->timestamp = new \DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param \DateTime $timestamp
     *
     * @return $this
     */
    public function setTimestamp(\DateTime $timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->summary;
    }
}