<?php

namespace AppBundle\Entity\Relationships;

// comment
use AppBundle\Entity\Comment;
// media
use AppBundle\Entity\Media;

/**
 * Trait CommentsHaveMedia
 *
 * This trait defines the OWNING side of a ManyToOne relationship.
 *
 * 1. Requires `Media` entity to implement `$comments` property with `OneToMany` and `mappedBy="comments"` annotation.
 * 2. Requires `Media` entity to implement linkComment(Comment $comment) public method.
 * 3. Requires `Media` entity to have `cascade={"remove"}` to avoid orphan objects on `Media` deletion.
 * 4. `cascade={"persist"}` on this side on the relationship is fine (applies to one `Media` only).
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait CommentsHaveMedia
{
    /**
     * @var Media
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Media", inversedBy="comments", cascade={"persist"})
     */
    private $media;

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * @param Media $media
     *
     * @return $this
     */
    public function setMedia(Media $media)
    {
        if ($media) {
            $this->linkMedia($media);
            $media->linkComment($this);
        } else {
            $this->unlinkMedia();
            $media->unlinkComment($this);
        }

        return $this;
    }

    /**
     * @param Media $media
     */
    public function linkMedia(Media $media)
    {
        $this->media = $media;
    }

    public function unLinkMedia()
    {
        $this->media = null;
    }
}
