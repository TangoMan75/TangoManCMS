<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Comment;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UserHasComments
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 1. Requires `Comment` entity to implement `$user` property with `ManyToOne` and `inversedBy="comments"` annotation.
 * 2. Requires `Comment` entity to implement linkUser(User $user) public method.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->comments = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait UserHasComments
{
    /**
     * @var array|Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $comments = [];

    /**
     * @param array|Comment[]|ArrayCollection $comments
     *
     * @return $this
     */
    public function setComments($comments)
    {
        foreach (array_diff($this->comments, $comments) as $comment) {
            $this->unlinkComment($comment);
        }

        foreach ($comments as $comment) {
            $this->linkComment($comment);
        }

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
        if ($this->comments->contains($comment)) {
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
        $comment->unlinkUser();

        return $this;
    }

    /**
     * @param Comment $comment
     */
    public function linkComment(Comment $comment)
    {
        if (!$this->comments->contains($comment)) {
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
