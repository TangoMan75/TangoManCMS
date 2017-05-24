<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Media;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait StatHasMedias
 *
 * This trait defines the INVERSE side of a OneToMany relationship.
 * 
 * 1. Requires `Media` entity to implement `$stat` property with `ManyToOne` and `inversedBy="medias"` annotation.
 * 2. Requires `Media` entity to implement linkStat(Stat $stat) public method.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->medias = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait StatHasMedias
{
    /**
     * @var array|Media[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Media", mappedBy="stat", cascade={"persist"})
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
        if (in_array($media, (array)$this->medias)) {
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
        $media->linkStat($this);

        return $this;
    }

    /**
     * @param Media $media
     */
    public function linkMedia(Media $media)
    {
        if (!in_array($media, (array)$this->medias)) {
            $this->medias[] = $media;
        }
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function removeMedia(Media $media)
    {
        $this->unlinkMedia($media);
        $media->unlinkStat($this);

        return $this;
    }

    /**
     * @param Media $media
     */
    public function unlinkMedia(Media $media)
    {
        $this->medias->removeElement($media);
    }
}
