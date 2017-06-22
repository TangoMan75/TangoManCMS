<?php

namespace AppBundle\Entity\Relationships;

// post
use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait SectionsHavePosts
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 1. Requires owned `Post` entity to implement `$sections` property with `ManyToMany` and `mappedBy="posts"` annotation.
 * 2. Requires owned `Post` entity to implement `linkSection` and `unlinkSection` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait SectionsHavePosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", inversedBy="sections", cascade={"persist"})
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
        $post->linkSection($this);

        return $this;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function removePost(Post $post)
    {
        $this->unlinkPost($post);
        $post->unlinkSection($this);

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
     */
    public function unlinkPost(Post $post)
    {
        $this->posts->removeElement($post);
    }
}
