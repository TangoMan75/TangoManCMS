<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait StatHasPosts
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 
 * 1. Requires `Post` entity to implement `$stat` property with `ManyToOne` and `inversedBy="posts"` annotation.
 * 2. Requires `Post` entity to implement linkStat(Stat $stat) public method.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait StatHasPosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="stat", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts = [];

    /**
     * @param array|Post[]|ArrayCollection $posts
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
        $this->linkPost($post);
        $post->linkStat($this);

        return $this;
    }

    /**
     * @param Post $post
     */
    public function linkPost(Post $post)
    {
        if (!in_array($post, (array)$this->posts)) {
            $this->posts[] = $post;
        }
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function removePost(Post $post)
    {
        $this->unlinkPost($post);
        $post->unlinkStat($this);

        return $this;
    }

    /**
     * @param Post $post
     */
    public function unlinkPost(Post $post)
    {
        $this->posts->removeElement($post);
    }
}
