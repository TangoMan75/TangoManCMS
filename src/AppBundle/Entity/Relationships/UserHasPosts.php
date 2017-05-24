<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Post;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UserHasPosts
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 1. Requires `Post` entity to implement `$user` property with `ManyToOne` and `inversed="posts"` annotation.
 * 2. Requires `Post` entity to implement `linkUser` and `unlinkUser` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait UserHasPosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user", cascade={"remove", "persist"})
     * @ORM\OrderBy({"created"="DESC"})
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
        $post->linkUser($this);

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

    /**
     * @param Post $post
     */
    public function linkPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
        }
    }
}
