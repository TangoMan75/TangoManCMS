<?php

namespace AppBundle\Entity\Traits;

/**
 * Class Categorized
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity\Traits
 */
Trait Categorized
{
    /**
     * @var array
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $categories = [];

    /**
     * @return bool
     */
    public function hasCategory($category)
    {
        if (in_array($category, $this->categories)) {
            return true;
        }

        return false;
    }

    /**
     * @param String $category
     */
    public function addCategory($category)
    {
        if (!in_array($category, $this->categories)) {
            array_push($this->categories, $category);
        }

        return $this;
    }

    /**
     * @param String $category
     */
    public function removeCategory($category)
    {
        $categories = $this->categories;
        if (in_array($category, $categories)) {
            $remove[] = $category;
            $this->categories = array_diff($categories, $remove);
        }
    }
}