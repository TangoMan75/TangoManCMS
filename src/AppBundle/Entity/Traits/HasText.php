<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait HasText
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasText
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     *
     * @return $this
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }
}
