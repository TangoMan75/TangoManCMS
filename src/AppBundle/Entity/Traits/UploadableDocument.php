<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class UploadableDocument
 * 1. Requires entities to own "Categorized", "Timestampable" and "Sluggable" traits.
 * 2. Requires entities to be marked with "Uploadable" annotation.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait UploadableDocument
{
    /**
     * @Vich\UploadableField(mapping="documents", fileNameProperty="documentFileName", size="documentSize")
     * @Assert\File(maxSize="100M", mimeTypes={
     *     "application/msword",
     *     "application/pdf",
     *     "application/vnd.ms-excel",
     *     "application/vnd.ms-powerpoint",
     *     "application/vnd.openxmlformats-officedocument.presentationml.presentation",
     *     "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
     *     "application/zip"
     * })
     * @var File
     */
    private $documentFile;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $documentFileName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var integer
     */
    private $documentSize;

    /**
     * @return String
     */
    public function getDocumentFile()
    {
        return $this->documentFile;
    }

    /**
     * @param File|null $documentFile
     *
     * @return $this
     */
    public function setDocumentFile(File $documentFile = null)
    {
        $this->documentFile = $documentFile;

        if ($documentFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->modified = new \DateTimeImmutable();
        }

        return $this;
    }

    /**
     * @return String
     */
    public function getDocumentFileName()
    {
        return $this->documentFileName;
    }

    /**
     * Generates file name from slug
     *
     * @return String|null
     */
    public function getDocumentPrettyFileName()
    {
        $extension = explode('.', $this->documentFileName);

        // Returns string left part before last dash
        return substr($this->slug, 0, strrpos($this->slug, '-')).'.'.end($extension);
    }

    /**
     * @param String $documentFileName
     *
     * @return $this
     */
    public function setDocumentFileName($documentFileName)
    {
        $this->documentFileName = $documentFileName;

        if ($documentFileName) {
            $this->setDocument('/uploads/documents/'.$documentFileName);
        } else {
            $this->setDocument(NULL);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getDocumentSize()
    {
        return $this->documentSize;
    }

    /**
     * @param int $documentSize
     *
     * @return $this
     */
    public function setDocumentSize($documentSize)
    {
        $this->documentSize = $documentSize;

        return $this;
    }

    /**
     * Delete image file and cached thumbnail
     * @ORM\PreRemove()
     */
    public function deleteDocumentFile()
    {
        if ($this->hasCategory('document')) {
            // Get file path
            $path = __DIR__."/../../../web".$this->getLink();
            // Delete file if exists
            if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
