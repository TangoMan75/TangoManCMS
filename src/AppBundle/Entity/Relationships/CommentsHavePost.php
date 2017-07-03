<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Post;

/**
 * Trait CommentsHavePost
 * This trait defines the OWNING side of a ManyToOne relationship.
 * 1. Requires `Post` entity to implement `$comments` property with `OneToMany` and `mappedBy="comments"` annotation.
 * 2. Requires `Post` entity to implement linkComment(Comment $comment) public method.
 * 3. Requires `Post` entity to have `cascade={"remove"}` to avoid orphan objects on `Post` deletion.
 * 4. `cascade={"persist"}` on this side on the relationship is fine (applies to one `Post` only).
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait CommentsHavePost
{
    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="comments", cascade={"persist"})
     */
    private $post;

    /**
     * @param Post $post
     *
     * @return $this
     */
    public function setPost(Post $post)
    {
        if ($post) {
            $this->linkPost($post);
            $post->linkComment($this);
        } else {
            $this->unlinkPost();
            $post->unlinkComment($this);
        }

        return $this;
    }

    /**
     * @return Post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function linkPost(Post $post)
    {
        $this->post = $post;
    }

    public function unLinkPost()
    {
        $this->post = null;
    }
}
