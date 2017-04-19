<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Tag;

/**
 * Class Taggable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Taggable
{
    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="items", fetch="EAGER")
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
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
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
