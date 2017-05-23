<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UserHasComments
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 1. Requires `Comment` entity to implement `$user` property with `ManyToOne` and `inversedBy="comments"` annotation.
 * 2. Requires `Comment` entity to implement `linkUser` and `unlinkUser` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->comments = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait UserHasComments
{
    /**
     * @var array|Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user", cascade={"remove", "persist"})
     * @ORM\OrderBy({"created"="DESC"})
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
        $this->linkComment($comment);
        $comment->linkUser($this);

        return $this;
    }

    /**
     * @param Comment $comment
     *
     * @return $this
     */
    public function removeComment(Comment $comment)
    {
        $this->unlinkComment($comment);
        $comment->unlinkUser($this);

        return $this;
    }

    /**
     * @param Comment $comment
     */
    public function linkComment(Comment $comment)
    {
        if (!in_array($comment, (array)$this->comments)) {
            $this->comments[] = $comment;
        }
    }

    /**
     * @param Comment $comment
     */
    public function unlinkComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }
}
