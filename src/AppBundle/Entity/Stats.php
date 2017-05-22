<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Hit
 * @ORM\Table(name="stats")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatsRepository")
 */
class Stats
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $sessionId;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $likes;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stars;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $upVotes;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $downVotes;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $sessionId
     *
     * @return Hit
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * @return int
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param integer $views
     *
     * @return Hit
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @return $this
     */
    public function addView()
    {
        $this->views = ++$this->views;

        return $this;
    }

    /**
     * @param integer $likes
     *
     * @return Hit
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * @return int
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @return $this
     */
    public function addLike()
    {
        $this->likes = ++$this->likes;

        return $this;
    }

    /**
     * @param integer $stars
     *
     * @return Hit
     */
    public function setStars($stars)
    {
        if (!($stars < 0 || $stars > 5)) {
            $this->stars = $stars;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getStars()
    {
        return $this->stars;
    }

    /**
     * @param integer $upVotes
     *
     * @return Hit
     */
    public function setUpVotes($upVotes)
    {
        $this->upVotes = $upVotes;

        return $this;
    }

    /**
     * @return int
     */
    public function getUpVotes()
    {
        return $this->upVotes;
    }

    /**
     * @return $this
     */
    public function addUpVote()
    {
        $this->upVotes = ++$this->upVotes;

        return $this;
    }

    /**
     * @param integer $downVotes
     *
     * @return Hit
     */
    public function setDownVotes($downVotes)
    {
        $this->downVotes = $downVotes;

        return $this;
    }

    /**
     * @return int
     */
    public function getDownVotes()
    {
        return $this->downVotes;
    }

    /**
     * @return $this
     */
    public function addDownVote()
    {
        $this->downVotes = ++$this->downVotes;

        return $this;
    }
}
