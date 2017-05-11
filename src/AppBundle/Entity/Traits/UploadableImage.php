<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Trait UploadableImage
 * 1. Requires entity to own "Categorized", "Timestampable", "Illustrable" and "Sluggable" traits.
 * 2. Requires entity to be marked with "Uploadable" annotation.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait UploadableImage
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $image;

    /**
     * @Vich\UploadableField(mapping="image_upload", fileNameProperty="imageFileName")
     * @Assert\File(maxSize="2M", mimeTypes={
     *     "image/gif",
     *     "image/jpeg",
     *     "image/jpg",
     *     "image/png"
     * })
     * @var File
     */
    private $imageFile;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $imageFileName;

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     *
     * @return $this
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * imageFile property is not persisted!
     *
     * @return String
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }

    /**
     * imageFile property is not persisted!
     *
     * @param File|null $imageFile
     *
     * @return $this
     */
    public function setImageFile(File $imageFile = null)
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->modified = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return String
     */
    public function getImageFileName()
    {
        return $this->imageFileName;
    }

    /**
     * Generates file name from slug
     *
     * @return String|null
     */
    public function getImagePrettyFileName()
    {
        $extension = explode('.', $this->imageFileName);

        // Returns string left part before last dash
        return substr($this->slug, 0, strrpos($this->slug, '-')).'.'.end($extension);
    }

    /**
     * @param String $imageFileName
     *
     * @return $this
     */
    public function setImageFileName($imageFileName)
    {
        $this->imageFileName = $imageFileName;

        if ($imageFileName) {
            $this->setImage('/uploads/images/'.$imageFileName);
            if (!$this->link) {
                $this->link = '/uploads/images/'.$imageFileName;
            }
        } else {
            // Remove deleted file from database
            if ($this->link == $this->image) {
                $this->link = null;
            }
            $this->setImage(null);
        }

        return $this;
    }

    /**
     * Delete image file and cached thumbnail
     * @ORM\PreRemove()
     */
    public function deleteImageFile()
    {
        if ($this->hasCategory('photo') || $this->hasCategory('thetas')) {
            // Get thumbnail path
            $path = __DIR__."/../../../web/media/cache/thumbnail".$this->getImage();

            // Delete file if exists
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
