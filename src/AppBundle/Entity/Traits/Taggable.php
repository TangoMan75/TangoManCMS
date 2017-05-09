<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Tag;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait Taggable
 * 
 * This trait defines the INVERSE side of the relationship.
 * 
 * 1. Requires `Owner` entity to implement `$items` property with `ManyToMany` and `mappedBy="items"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->owners = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Taggable
{
    /**
     * @var array|Tag[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="items")
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
     * @return array|Tag[]|ArrayCollection
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
        if (!in_array($tag, (array)$this->tags)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function removeTag(Tag $tag)
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
