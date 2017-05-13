<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Media;
use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait MediaHasSections
 * 
 * This trait defines the OWNING side of a ManyToMany relationship.
 * 
 * 1. Requires `Section` entity to implement `$medias` property with `ManyToMany` and `mappedBy="medias"` annotation.
 * 2. Requires `Section` entity to implement `linkMedia` and `unlinkMedia` methods.
 * 3. (Optional) entity constructor must initialize ArrayCollection object
 *     $this->sections = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait MediaHasSections
{
    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", inversedBy="medias", cascade={"persist"})
     */
    private $sections = [];

    /**
     * @param array|Section[]|ArrayCollection $sections
     *
     * @return $this
     */
    public function setSections($sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * @return array|Section[]|ArrayCollection
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
        $this->linkSection($section);
        $section->linkMedia($this);

        return $this;
    }

    /**
     * @param Section $section
     *
     * @return $this
     */
    public function removeSection(Section $section)
    {
        $this->unlinkSection($section);
        $section->unlinkMedia($this);

        return $this;
    }

    /**
     * @param Section $section
     */
    public function linkSection(Section $section)
    {
        if (!in_array($section, (array)$this->sections)) {
            $this->sections[] = $section;
        }
    }

    /**
     * @param Section $section
     */
    public function unlinkSection(Section $section)
    {
        $this->sections->removeElement($section);
    }
}
