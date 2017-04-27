<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Base64Illustrable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Base64Illustrable
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $base64Image;

    /**
     * @return string
     */
    public function getBase64Image()
    {
        return $this->base64Image;
    }

    /**
     * @param string $base64Image
     *
     * @return $this
     */
    public function setBase64Image($base64Image)
    {
        $this->base64Image = $base64Image;

        return $this;
    }
}
