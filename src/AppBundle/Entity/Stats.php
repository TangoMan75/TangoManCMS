<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Page;
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
     * @var int
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Page", mappedBy="stats")
     */
    private $pages = [];

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="stats")
     */
    private $posts = [];

    /**
     * @var User
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\User", mappedBy="stats")
     */
    private $users = [];

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
     * Stats constructor.
     */
    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->users = new ArrayCollection();
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
     * @param Page[]|ArrayCollection $pages
     *
     * @return $this
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * @return array|Page[]|ArrayCollection $pages
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param Page $page
     *
     * @return bool
     */
    public function hasPage(Page $page)
    {
        if (in_array($page, (array)$this->pages)) {
            return true;
        }

        return false;
    }

    /**
     * @param Page $page
     *
     * @return $this
     */
    public function addPage(Page $page)
    {
        if (!in_array($page, (array)$this->pages)) {
            $this->pages[] = $page;
        }

        return $this;
    }

    /**
     * @param Post[]|ArrayCollection $posts
     *
     * @return $this
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * @return array|Post[]|ArrayCollection $posts
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param Post $post
     *
     * @return bool
     */
    public function hasPost(Post $post)
    {
        if (in_array($post, (array)$this->posts)) {
            return true;
        }

        return false;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function addPost(Post $post)
    {
        if (!in_array($post, (array)$this->posts)) {
            $this->posts[] = $post;
        }

        return $this;
    }

    /**
     * @param User[]|ArrayCollection $users
     *
     * @return $this
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * @return array|User[]|ArrayCollection $users
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function hasUser(User $user)
    {
        if (in_array($user, (array)$this->users)) {
            return true;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function addUser(User $user)
    {
        if (!in_array($user, (array)$this->users)) {
            $this->users[] = $user;
        }

        return $this;
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
