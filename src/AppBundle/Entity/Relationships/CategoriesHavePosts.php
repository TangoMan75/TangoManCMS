<?php

namespace AppBundle\Entity\Relationships;

// category
use AppBundle\Entity\Category;
// post
use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait CategoriesHavePosts
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 1. Requires `Post` entity to implement `$categories` property with `ManyToMany` and `inversedBy="posts"` annotation.
 * 2. Requires `Post` entity to implement `linkCategory` and `unlinkCategory` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait CategoriesHavePosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="categories", cascade={"persist"})
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
     * @return $this
     */
    public function addPost(Post $post)
    {
        $this->linkPost($post);
        $post->linkCategory($this);

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
        $post->unlinkCategory($this);

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
