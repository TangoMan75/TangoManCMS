<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Post;

/**
 * Trait HasPosts
 *
 * This trait defines the OWNING side of the relationship.
 * 
 * 1. Requires owned `Post` entity to implement `$owners` property with `ManyToMany` and `inversedBy="posts"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasPosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="sections")
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
        if (!in_array($post, (array)$this->posts)) {
            $this->posts[] = $post;
        }

        return $this;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function removePost(Post $post)
    {
        $this->posts->removeElement($post);

        return $this;
    }
}
