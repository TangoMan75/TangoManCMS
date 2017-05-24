<?php

namespace AppBundle\Entity\Relationships;

use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait TagsHaveSections
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Section` entity to implement `$tags` property with `ManyToMany` and `mappedBy="tags"` annotation.
 * 2. (Optional) Entities constructors must initialize ArrayCollection object
 *     $this->sections = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait TagsHaveSections
{
    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", inversedBy="tags", cascade={"persist"})
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
        $section->linkTag($this);

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
        $this->unlinkSection($section);
        $section->unlinkTag($this);

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
