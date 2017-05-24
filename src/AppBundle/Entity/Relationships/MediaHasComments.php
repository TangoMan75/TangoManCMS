<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait MediaHasComments
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 1. Requires `Comment` entity to implement `$media` property with `ManyToOne` and `inversedBy="comments"` annotation.
 * 2. Requires `Comment` entity to implement `linkMedia` and `unlinkMedia` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->comments = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait MediaHasComments
{
    /**
     * @var array|Comment[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="media", cascade={"remove", "persist"})
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
        if ($this->comments->contains($comment))) {
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
        $comment->linkMedia($this);

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
        $comment->unlinkMedia($this);

        return $this;
    }

    /**
     * @param Comment $comment
     */
    public function linkComment(Comment $comment)
    {
        if (!$this->comments->contains($comment))) {
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
