<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vote
 * @ORM\Table(name="vote")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VoteRepository")
 */
class Vote
{
    use Relationships\VotesHavePost;
    use Relationships\VotesHaveUser;

    /**
     * @var int
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $thumbUp;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $thumbDown;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $stars;

    /**
     * Stat constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return $this
     */
    public function setThumbDown()
    {
        $this->thumbUp = false;
        $this->thumbDown = true;

        return $this;
    }

    /**
     * @return int
     */
    public function getThumbDown()
    {
        return $this->thumbDown;
    }

    /**
     * @return $this
     */
    public function setThumbUp()
    {
        $this->thumbUp = true;
        $this->thumbDown = false;

        return $this;
    }

    /**
     * @return int
     */
    public function getThumbUp()
    {
        return $this->thumbUp;
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
}
