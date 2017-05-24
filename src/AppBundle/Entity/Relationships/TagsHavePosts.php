<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait TagsHavePosts
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Post` entity to implement `$tags` property with `ManyToMany` and `inversedBy="tags"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait TagsHavePosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="tags", cascade={"persist"})
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
     * @return array|Post[]|ArrayCollection
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
        if ($this->posts->contains($post)) {
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
        $post->linkTag($this);

        return $this;
    }

    /**
     * @param Post $post
     */
    public function linkPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
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
        $post->unlinkTag($this);

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