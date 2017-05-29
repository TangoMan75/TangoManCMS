<?php

namespace AppBundle\Entity\Relationships;

// tag
use AppBundle\Entity\Tag;
// section
use AppBundle\Entity\Section;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Trait TagsHaveSections
 *
 * This trait defines the INVERSE side of a ManyToMany relationship.
 *
 * 1. Requires `Section` entity to implement `$tags` property with `ManyToMany` and `inversedBy="sections"` annotation.
 * 2. Requires `Section` entity to implement `linkTag` and `unlinkTag` methods.
 * 3. Requires formType to own `'by_reference => false,` attribute to force use of `add` and `remove` methods.
 * 4. Entity constructor must initialize ArrayCollection object
 *     $this->sections = new ArrayCollection();
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Relationships
 */
trait TagsHaveSections
{
    /**
     * @var array|Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", mappedBy="tags", cascade={"persist"})
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
