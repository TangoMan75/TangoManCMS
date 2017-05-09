<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Post;

/**
 * Trait HasPost
 * 
 * This trait defines the INVERSE side of the relationship.
 * 
 * Requires `Post` entity to implement `$comments` property with `OneToMany` and `mappedBy="comments"` annotation.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasPost
{
    /**
     * @var Post
     */
    private $post;

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function setPost(Post $post)
    {
        $this->post = $post;

        return $this;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }
}
