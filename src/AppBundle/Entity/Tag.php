<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Post;
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Tag
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="tag")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Tag
{
    use Relationships\TagsHavePages;
    use Relationships\TagsHavePosts;
    use Relationships\TagsHaveSections;

    use Traits\HasLabel;
    use Traits\HasName;
    use Traits\HasType;
    use Traits\Slugify;

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