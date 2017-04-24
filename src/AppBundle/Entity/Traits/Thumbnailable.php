<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Thumbnailable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Thumbnailable
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $thumbnail;

    /**
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param string $thumbnail
     *
     * @return $this
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Setsd default thumbnail
     * @ORM\PrePersist()
     *
     * @return $this
     */
    public function setDefaultThumbnail()
    {
        if (!$this->thumbnail) {
            $imageFile = $this->getImageFile();
            if ($imageFile) {
                $this->setThumbnail($imageFile);
            }
        }

        return $this;
    }
}
