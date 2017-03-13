<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Tag;

Trait Tagable
{
    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="items")
     */
    private $tags = [];

    /**
     * @return Tag[]
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
        if (in_array($tag, $this->tags)) {
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
        // Only one of each is allowed
        if (!in_array($tag, $this->tags)) {
            array_push($this->tags, $tag);
        }

        return $this;
    }

    /**
     * @param tag $tag
     *
     * @return $this
     */
    public function removeTag(tag $tag)
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
