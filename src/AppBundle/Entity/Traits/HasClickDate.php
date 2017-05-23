<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait HasClickDate
 * Adds "clickDate" timestamp to entity.
 * Entity constructor must initialize "DateTimeImmutable" object
 *     $this->clickDate = new \DateTimeImmutable();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait HasClickDate
{
    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $clickDate;

    /**
     * @return \DateTime
     */
    public function getClickDate()
    {
        return $this->clickDate;
    }

    /**
     * @param \DateTime $clickDate
     *
     * @return $this
     */
    public function setClickDate(\DateTimeImmutable $clickDate)
    {
        $this->clickDate = $clickDate;

        return $this;
    }
}
