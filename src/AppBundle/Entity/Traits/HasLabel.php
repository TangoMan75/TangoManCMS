<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait HasLabel
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
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
        } else {
            return $this->label;
        }
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