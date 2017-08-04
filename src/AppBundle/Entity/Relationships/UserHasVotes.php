<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Vote;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait UserHasVotes
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 1. Requires `Vote` entity to implement `$user` property with `ManyToOne` and `inversedBy="votes"` annotation.
 * 2. Requires `Vote` entity to implement linkUser(User $user) public method.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->votes = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait UserHasVotes
{
    /**
     * @var array|Vote[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vote", mappedBy="user", cascade={"persist", "remove"})
     */
    private $votes = [];

    /**
     * @param array|Vote[]|ArrayCollection $votes
     *
     * @return $this
     */
    public function setVotes($votes)
    {
        foreach (array_diff($this->votes, $votes) as $vote) {
            $this->unlinkVote($vote);
        }

        foreach ($votes as $vote) {
            $this->linkVote($vote);
        }

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
        $vote->linkUser($this);

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
        $vote->unlinkUser();

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
