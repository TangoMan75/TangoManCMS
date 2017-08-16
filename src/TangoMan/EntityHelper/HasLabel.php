<?php

namespace TangoMan\EntityHelper;

/**
 * Trait HasLabel
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait HasLabel
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $label;

    /**
     * @return string
     */
    public function getLabel()
    {
        if (!$this->label) {
            return 'default';
        }

        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        if (in_array(
            $label,
            [
                'default',
                'primary',
                'info',
                'success',
                'warning',
                'danger',
            ]
        )) {
            $this->label = $label;
        }

        return $this;
    }
}
