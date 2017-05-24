<?php

namespace AppBundle\Entity\Relationships;

// media
use AppBundle\Entity\Media;
// section
use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait MediasHaveSections
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Section` entity to implement `$medias` property with `ManyToMany` and `inversedBy="sections"` annotation.
 * 2. Requires `Section` entity to implement `linkMedia` and `unlinkMedia` methods.
 * 3. (Optional) Entity constructor must initialize ArrayCollection object
 *     $this->sections = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait MediasHaveSections
{
    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", mappedBy="medias")
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
        if ($this->sections->contains($section)) {
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
        if (!$this->sections->contains($section)) {
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
