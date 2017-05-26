<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait HasViews
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait HasViews
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $views;

    /**
     * @param integer $views
     *
     * @return $this
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return int
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @return $this
     */
    public function addView()
    {
        $this->views = ++$this->views;

        return $this;
    }
}
