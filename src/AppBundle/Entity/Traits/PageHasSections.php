<?php

namespace AppBundle\Entity\Traits;

use AppBundle\Entity\Section;
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait PageHasSections
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 * 
 * 1. Requires `Section` entity to implement `$pages` property with `ManyToMany` and `inversedBy="sections"` annotation.
 * 2. Requires `Section` entity to implement `linkPage` and `unlinkPage` methods.
 * 3. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->sections = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait PageHasSections
{
    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", mappedBy="pages", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
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
     * @return array|Section[]|ArrayCollection $sections
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
        $section->linkSection($this);
        $this->linkPage($section);

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
     *
     * @return $this
     */
    public function removeSection(Section $section)
    {
        $section->unlinkSection($this);
        $this->unlinkPage($section);

        return $this;
    }

    /**
     * @param Section $section
     */
    public function unlinkSection(Section $section)
    {
        $this->sections->removeElement($section);
    }
}
