<?php

namespace AppBundle\Entity\Relationships;

// tag
use AppBundle\Entity\Tag;
// page
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PagesHaveTags
 *
 * This trait defines the OWNING side of a ManyToMany relationship.
 *
 * 1. Requires owned `Tag` entity to implement `$pages` property with `ManyToMany` and `mappedBy="tags"` annotation.
 * 2. Requires owned `Tag` entity to implement `linkPage` and `unlinkPage` methods.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->tags = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait PagesHaveTags
{
    /**
     * @var array|Tag[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="pages")
     */
    private $tags = [];

    /**
     * @param array|Tag[]|ArrayCollection $tags
     *
     * @return $this
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return array|Tag[]|ArrayCollection $tags
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return bool
     */
    public function hasTag(Tag $tag)
    {
        if ($this->tags->contains($tag)) {
            return true;
        }

        return false;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        $this->linkTag($tag);
        $tag->linkPage($this);

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $this->unlinkTag($tag);
        $tag->unlinkPage($this);

        return $this;
    }

    /**
     * @param Tag $tag
     */
    public function linkTag(Tag $tag)
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @param Tag $tag
     */
    public function unlinkTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }
}
