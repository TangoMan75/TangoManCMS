<?php

namespace AppBundle\Entity\Traits;

/**
 * Trait HasTitle
 * Requires entity to own "Sluggable" and "Timestampable" traits.
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait HasTitle
{
    /**
     * @var String
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var String
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $subtitle;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        if (!$this->title) {
            $this->title = $this->created->format('d/m/Y H:i:s');
        }

        if (!$this->slug) {
            $this->setUniqueSlug($this->title);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $subtitle
     *
     * @return $this
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }
}
