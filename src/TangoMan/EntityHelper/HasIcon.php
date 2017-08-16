<?php

namespace TangoMan\EntityHelper;

/**
 * Trait HasIcon
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
Trait HasIcon
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }
}
