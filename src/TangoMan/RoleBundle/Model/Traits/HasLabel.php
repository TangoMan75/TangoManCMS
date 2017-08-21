<?php

namespace TangoMan\RoleBundle\Model\Traits;

/**
 * Trait HasLabel
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\RoleBundle\Model\Traits
 */
trait HasLabel
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $label;

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
