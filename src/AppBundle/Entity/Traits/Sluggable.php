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
    /**
     * @var string slug
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
        $this->slug = $this->slugify($string.'-'.uniqid());

        return $this;
    }

    /**
     * Generates slug from string
     *
     * @param  String  $string                       Source string
     * @param  Boolean $removeWesternEuropeanAccents [optional] When true, removes western european accents (ex. é => e) - default: true
     * @param  String  $separator                    [optional] Character to use to replace non-alphanum chars - default: '-'
     *
     * @return String  clean string
     */
    public function slugify($string, $removeWesternEuropeanAccents = true, $separator = '-')
    {
        $slug = trim($string);
        if ($removeWesternEuropeanAccents) {
            // That may seem weird but this seems to be the better way to do it
            $slug = htmlentities($slug, ENT_NOQUOTES, 'UTF-8');
            $slug = preg_replace('/&#?([a-zA-Z])[a-zA-Z0-9]*;/i', '${1}', $slug);
        }
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $slug);
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);
        $slug = strtolower(trim($slug, $separator));
        $slug = preg_replace("/[\/_|+ -]+/", $separator, $slug);

        return $slug;
    }
}
