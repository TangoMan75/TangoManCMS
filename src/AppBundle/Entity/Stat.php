<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Page;
use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Stat
 * @ORM\Table(name="stat")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatRepository")
 */
class Stat
{
    use Relationships\StatHasMedias;
    use Relationships\StatHasPages;
    use Relationships\StatHasPosts;
    use Relationships\StatHasUsers;

    /**
     * @var int
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
    private $dislikes;

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
        $this->viewDate = new \DateTimeImmutable();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @param integer $dislikes
     *
     * @return $this
     */
    public function setDislikes($dislikes)
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    /**
     * @return int
     */
    public function getDislikes()
    {
        return $this->dislikes;
    }

    /**
     * @return $this
     */
    public function addDislike()
    {
        $this->dislikes = ++$this->dislikes;

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
}
