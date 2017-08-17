<?php

namespace TangoMan\EntityHelper;

/**
 * Trait Sluggable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package TangoMan\EntityHelper
 */
trait Sluggable
{
    use Slugify;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $slug;

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Slug is generated from given string
     *
     * @param string $string
     *
     * @return $this
     */
    public function setSlug($string)
    {
        $this->slug = $this->slugify($string);

        return $this;
    }

    /**
     * Unique slug is generated from given string
     *
     * @param string $string
     *
     * @return $this
     */
    public function setUniqueSlug($string)
    {
        $this->slug = $this->slugify($string.'-'.uniqid());

        return $this;
    }
}
