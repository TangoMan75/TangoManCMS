<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Downloadable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Downloadable
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $document;

    /**
     * @return string
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * @param string $document
     *
     * @return $this
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }
}
