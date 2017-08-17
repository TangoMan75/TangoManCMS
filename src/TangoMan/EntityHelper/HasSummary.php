<?php

namespace TangoMan\EntityHelper;

/**
 * Trait HasSummary
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait HasSummary
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

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
