<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class HasSections
 * 
 * This trait defines the OWNING side of the relationship.
 * 
 * 1. Section entity must implement inversed property and methods.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->sections = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait HasSections
{
    /**
     * @var Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", inversedBy="contents")
     */
    private $sections = [];

    /**
     * @param Section[]|ArrayCollection $sections
     *
     * @return $this
     */
    public function setSections($sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * @return Section[]
     */
    public function getSections()
    {
        return $this->sections;
    }

    /**
     * @param Section $section
     *
     * @return bool
     */
    public function hasSection(Section $section)
    {
        if (in_array($section, (array)$this->sections)) {
            return true;
        }

        return false;
    }

    /**
     * @param Section $section
     *
     * @return $this
     */
    public function addSection(Section $section)
    {
        if (!in_array($section, (array)$this->sections)) {
            $this->sections[] = $section;
        }

        return $this;
    }

    /**
     * @param section $section
     *
     * @return $this
     */
    public function removeSection(section $section)
    {
        $this->sections->removeElement($section);

        return $this;
    }
}
