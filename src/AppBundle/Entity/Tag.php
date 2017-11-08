<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Post;
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

use TangoMan\EntityHelper\Traits\HasLabel;
use TangoMan\EntityHelper\Traits\HasName;
use TangoMan\EntityHelper\Traits\HasType;
use TangoMan\EntityHelper\Traits\Slugify;

/**
 * Class Tag
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Tag
{
    use Relationships\TagsHavePages;
    use Relationships\TagsHavePosts;
    use Relationships\TagsHaveSections;
    use Relationships\TagsHaveSites;

    use HasLabel;
    use HasName;
    use HasType;
    use Slugify;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->pages = new ArrayCollection();
        $this->posts = new ArrayCollection();
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
     * @ORM\PreFlush()
     */
    public function setDefaults()
    {
        if (!$this->type) {
            $this->setType(strtolower($this->name));
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}