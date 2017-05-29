<?php

namespace AppBundle\Entity\Relationships;

// vote
use AppBundle\Entity\Vote;
// post
use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PostHasVotes
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 *
 * 1. Requires `Vote` entity to implement `$post` property with `ManyToOne` and `inversedBy="votes"` annotation.
 * 2. Requires `Vote` entity to implement linkPost(Post $post) public method.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->votes = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait PostHasVotes
{
    /**
     * @var array|Vote[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vote", mappedBy="post", cascade={"persist"})
     */
    private $votes = [];

    /**
     * @param array|Vote[]|ArrayCollection $votes
     *
     * @return $this
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;

        return $this;
    }

    /**
     * @return array|Vote[]|ArrayCollection $votes
     */
    public function getVotes()
    {
        return $this->votes;
    }

    /**
     * @param Vote $vote
     *
     * @return $this
     */
    public function addVote(Vote $vote)
    {
        $this->linkVote($vote);
        $vote->linkPost($this);

        return $this;
    }

    /**
     * @param Vote $vote
     *
     * @return $this
     */
    public function removeVote(Vote $vote)
    {
        $this->unlinkVote($vote);
        $vote->unlinkPost();

        return $this;
    }

    /**
     * @param Vote $vote
     */
    public function linkVote(Vote $vote)
    {
        if (!$this->votes->contains($vote)) {
            $this->votes[] = $vote;
        }
    }

    /**
     * @param Vote $vote
     */
    public function unlinkVote(Vote $vote)
    {
        $this->votes->removeElement($vote);
    }
}
