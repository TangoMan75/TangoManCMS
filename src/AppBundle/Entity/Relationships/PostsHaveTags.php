<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PostsHaveTags
 *
 * This trait defines the OWNING side of a ManyToMany relationship.
 *
 * 1. Requires owned `Tag` entity to implement `$posts` property with `ManyToMany` and `mappedBy="tags"` annotation.
 * 2. Requires owned `Tag` entity to implement `linkPost` and `unlinkPost` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->tags = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait PostsHaveTags
{
    /**
     * @var array|Tag[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="posts", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
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
        if (in_array($tag, (array)$this->tags)) {
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
        $tag->linkTag($this);
        $this->linkPost($tag);

        return $this;
    }

    /**
     * @param Tag $tag
     */
    public function linkTag(Tag $tag)
    {
        if (!in_array($tag, (array)$this->tags)) {
            $this->tags[] = $tag;
        }
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $tag->unlinkTag($this);
        $this->unlinkPost($tag);

        return $this;
    }

    /**
     * @param Tag $tag
     */
    public function unlinkTag(Tag $tag)
    {
        $this->tags->removeElement($tag);
    }
}
