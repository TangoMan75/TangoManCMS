<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasComments
 *
 * This trait defines the INVERSE side of the relationship.
 * 
 * 1. Requires entity with "HasComments" trait to own "comments" property marked with "OneToMany", 
 *     "mappedBy=`pages`" annotation defining desired relationships with target entity.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->comments = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasComments
{
    /**
     * @var array|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="commented")
     */
    private $comments = [];

    /**
     * @param array|ArrayCollection $comments
     *
     * @return $this
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return array|ArrayCollection $comments
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param $comment
     *
     * @return bool
     */
    public function hasComment($comment)
    {
        if (in_array($comment, (array)$this->comments)) {
            return true;
        }

        return false;
    }

    /**
     * @param $comment
     *
     * @return $this
     */
    public function addComment($comment)
    {
        if (!in_array($comment, (array)$this->comments)) {
            $this->comments[] = $comment;
        }

        return $this;
    }

    /**
     * @param $comment
     *
     * @return $this
     */
    public function removeComment($comment)
    {
        $this->comments->removeElement($comment);

        return $this;
    }
}
