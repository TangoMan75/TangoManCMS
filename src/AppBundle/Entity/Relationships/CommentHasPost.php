<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Post;

/**
 * Trait CommentHasPost
 * This trait defines the INVERSE side of a ManyToOne relationship.
 * 1. Requires `Post` entity to implement `$comments` property with `OneToMany` and `mappedBy="comments"` and cascade={"persist", "remove"} annotation.
 * 2. Requires `Post` entity to implement `linkComment` method.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
Trait CommentHasPost
{
    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="comments", cascade={"persist"})
     */
    private $post;

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function setPost(Post $post)
    {
        $this->linkPost($post);
        $post->linkComment($this);

        return $this;
    }

    /**
     * @param Post $post
     */
    public function linkPost(Post $post)
    {
        $this->post = $post;
    }
}
