<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait HasSections
 *
 * @package AppBundle\Entity\Traits
 */
Trait HasSections
{
    /**
     * @var Section[]
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
     * @param Section $section
     *
     * @return $this
     */
    public function removeSection(Section $section)
    {
        $this->sections->removeElement($section);

        return $this;
    }
}
