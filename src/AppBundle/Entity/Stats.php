<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Stats
 * @ORM\Table(name="stats")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatsRepository")
 */
class Stats
{
    /**
     * @var User
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     */
    private $user;

    /**
     * @var int
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Post")
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Page")
     */
    private $item;

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
     * @param integer $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param integer $views
     *
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
     * @return $this
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
