<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Comment;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait Commentable
 *
 * This trait defines the OWNING side of a OneToMany relationship.
 *
 * 1. Requires owned `Comment` entity to implement `$owner` property with `ManyToOne` and `inversedBy="comments"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->comments = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Commentable
{
    /**
     * @var array|Comment[]|ArrayCollection
     */
    private $comments = [];

    /**
     * @param array|Comment[]|ArrayCollection $comments
     *
     * @return $this
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return array|Comment[]|ArrayCollection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param Comment $comment
     *
     * @return bool
     */
    public function hasComment(Comment $comment)
    {
        if (in_array($comment, (array)$this->comments)) {
            return true;
        }

        return false;
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function addComment(Comment $comment)
    {
        $comment->link($this);
        $this->link($comment);

        return $this;
    }

    /**
     * @param Comment $comment
     */
    public function link(Comment $comment)
    {
        if (!in_array($comment, (array)$this->comments)) {
            $this->comments[] = $comment;
        }
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function removeComment(Comment $comment)
    {
        $comment->unlink($this);
        $this->unlink($comment);

        return $this;
    }

    /**
     * @param Comment $comment
     */
    public function unlink(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }
}
