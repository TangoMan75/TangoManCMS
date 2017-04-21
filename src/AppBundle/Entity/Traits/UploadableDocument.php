<?php

namespace AppBundle\Entity\Traits;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class UploadableDocument
 * Requires Timestampable trait
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait UploadableDocument
{
    /**
     * @Vich\UploadableField(mapping="documents", fileNameProperty="documentFileName")
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
     * @ORM\Column(type="integer")
     * @var integer
     */
    private $documentSize;

    /**
     * @return String
     */
    public function getDocumentFile()
    {
        return $this->documentFileName;
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
     * Builds file name from slug
     * @return String|null
     */
    public function getDocumentPrettyFileName()
    {
        $extension = explode('.', $this->documentFileName);
        // Returns string left part before last dash
        return substr($this->slug, 0, strrpos($this->slug, '-')) . '.' . end($extension);
    }

    /**
     * @param String $documentFileName
     *
     * @return $this
     */
    public function setDocumentFileName($documentFileName)
    {
        $this->documentFileName = $documentFileName;

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
}
