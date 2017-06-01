<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Category
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CategoryRepository")
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Category
{
    use Relationships\CategoriesHavePosts;
    use Relationships\CategoriesHaveSections;

    use Traits\HasName;
    use Traits\HasType;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Category constructor.
     */
    public function __construct()
    {
		$this->posts    = new ArrayCollection();
		$this->sections = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->type;
    }
}