<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Sluggable
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
trait Sluggable
{
    use Slugify;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

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
        if (!$string) {
            $string = $this->title;
        }

        $this->slug = $this->slugify($string.'-'.uniqid());

        return $this;
    }
}
