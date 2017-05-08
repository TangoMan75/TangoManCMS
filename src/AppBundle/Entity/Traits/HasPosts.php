<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasPosts
 * 
 * This trait defines the OWNING side of the relationship.
 * 
 * 1. Post entity must implement inversed property and methods.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasPosts
{
    /**
     * @var Post[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", inversedBy="contents")
     */
    private $posts = [];

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
     * @return Post[]
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
     * @param post $post
     *
     * @return $this
     */
    public function removePost(post $post)
    {
        $this->posts->removeElement($post);

        return $this;
    }
}
