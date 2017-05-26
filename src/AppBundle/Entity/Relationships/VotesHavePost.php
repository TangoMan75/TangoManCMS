<?php

namespace AppBundle\Entity\Relationships;

// vote
use AppBundle\Entity\Vote;
// post
use AppBundle\Entity\Post;

/**
 * Trait VotesHavePost
 *
 * This trait defines the OWNING side of a ManyToOne relationship.
 *
 * 1. Requires `Post` entity to implement `$votes` property with `OneToMany` and `mappedBy="votes"` annotation.
 * 2. Requires `Post` entity to implement linkVote(Vote $vote) public method.
 * 3. Requires `Post` entity to have `cascade={"remove"}` to avoid orphan objects on `Post` deletion.
 * 4. `cascade={"persist"}` on this side on the relationship is fine (applies to one `Post` only).
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait VotesHavePost
{
    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="votes", cascade={"persist"})
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
        if ($post) {
            $this->linkPost($post);
            $post->linkVote($this);
        } else {
            $this->unlinkPost();
            $post->unlinkVote($this);
        }

        return $this;
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
