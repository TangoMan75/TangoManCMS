<?php

namespace AppBundle\Entity\Traits;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasContents
 *
 * This trait defines the INVERSE side of the relationship.
 * 
 * 1. Requires entity with "HasContents" trait to own "contents" property marked with "ManyToMany", 
 *     "mappedBy=`pages`" annotation defining desired relationships with target entity.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->contents = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasContents
{
    /**
     * @var array|ArrayCollection
     */
    private $contents = [];

    /**
     * @param array|ArrayCollection $contents
     *
     * @return $this
     */
    public function setContents($contents)
    {
        $this->contents = $contents;

        return $this;
    }

    /**
     * @return array|ArrayCollection $contents
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param $content
     *
     * @return bool
     */
    public function hasContent($content)
    {
        if (in_array($content, (array)$this->contents)) {
            return true;
        }

        return false;
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function addContent($content)
    {
        if (!in_array($content, (array)$this->contents)) {
            $this->contents[] = $content;
        }

        return $this;
    }

    /**
     * @param $content
     *
     * @return $this
     */
    public function removeContent($content)
    {
        $this->contents->removeElement($content);

        return $this;
    }
}
