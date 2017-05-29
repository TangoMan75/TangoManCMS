<?php

namespace AppBundle\Entity\Relationships;

// media
use AppBundle\Entity\Media;
// section
use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait SectionsHaveMedias
 *
 * This trait defines the OWNING side of a ManyToMany relationship.
 *
 * 1. Requires owned `Media` entity to implement `$sections` property with `ManyToMany` and `mappedBy="medias"` annotation.
 * 2. Requires owned `Media` entity to implement `linkSection` and `unlinkSection` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->medias = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait SectionsHaveMedias
{
    /**
     * @var array|Media[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Media", inversedBy="sections", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $medias = [];

    /**
     * @param array|Media[]|ArrayCollection $medias
     *
     * @return $this
     */
    public function setMedias($medias)
    {
        $this->medias = $medias;

        return $this;
    }

    /**
     * @return array|Media[]|ArrayCollection $medias
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    public function hasMedia(Media $media)
    {
        if ($this->medias->contains($media)) {
            return true;
        }

        return false;
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function addMedia(Media $media)
    {
        $this->linkMedia($media);
        $media->linkSection($this);

        return $this;
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function removeMedia(Media $media)
    {
        $this->unlinkMedia($media);
        $media->unlinkSection($this);

        return $this;
    }

    /**
     * @param Media $media
     */
    public function linkMedia(Media $media)
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
        }
    }

    /**
     * @param Media $media
     */
    public function unlinkMedia(Media $media)
    {
        $this->medias->removeElement($media);
    }
}
