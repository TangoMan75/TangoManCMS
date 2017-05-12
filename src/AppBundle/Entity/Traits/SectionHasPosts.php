<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Post;
use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait SectionHasPosts
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 
 * 1. Requires `Post` entity to implement `$sections` property with `ManyToMany` and `inversedBy="posts"` annotation.
 * 2. Requires `Post` entity to implement `linkSection` and `unlinkSection` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->posts = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait SectionHasPosts
{
    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="sections", cascade={"persist"})
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
        $post->linkPost($this);
        $this->linkSection($post);

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
        $post->unlinkPost($this);
        $this->unlinkSection($post);

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
