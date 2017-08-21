<?php

namespace TangoMan\RoleBundle\Model\Traits;

/**
 * Trait HasType
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\RoleBundle\Model\Traits
 */
trait HasType
{
    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = strtoupper($type);

        return $this;
    }
}