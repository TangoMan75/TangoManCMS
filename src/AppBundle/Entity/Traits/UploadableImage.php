<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class UploadableImage
 * Requires Timestampable trait
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait UploadableImage
{
    /**
     * @Vich\UploadableField(mapping="upload", fileNameProperty="imageFileName")
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
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $imageSize;

    /**
     * @return String
     */
    public function getImageFile()
    {
        return $this->imageFileName;
    }

    /**
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
     * Builds file name from slug
     * @return String|null
     */
    public function getImagePrettyFileName()
    {
        $extension = explode('.', $this->imageFileName);
        // Returns string left part before last dash
        return substr($this->slug, 0, strrpos($this->slug, '-')) . '.' . end($extension);
    }

    /**
     * @param String $imageFileName
     *
     * @return $this
     */
    public function setImageFileName($imageFileName)
    {
        $this->imageFileName = $imageFileName;

        return $this;
    }

    /**
     * @return int
     */
    public function getImageSize()
    {
        return $this->imageSize;
    }

    /**
     * @param int $imageSize
     *
     * @return $this
     */
    public function setImageSize($imageSize)
    {
        $this->imageSize = $imageSize;

        return $this;
    }
}
