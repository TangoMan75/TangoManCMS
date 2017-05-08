<?php

namespace AppBundle\Entity\Traits;

/**
 * Class HasText
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasText
{
    /**
     * @var String
     * @ORM\Column(type="text", nullable=true)
     */
    private $title;

    /**
     * @var String
     * @ORM\Column(type="text", nullable=true)
     */
    private $summary;

    /**
     * @return string
     */
    public function getText()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setText($title)
    {
        $this->title = $title;

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
