<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Media;
use AppBundle\Entity\Post;
use AppBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Tag
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="tag")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\TagRepository")
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Tag
{
    use Relationships\TagsHaveMedias;
    use Relationships\TagsHavePages;
    use Relationships\TagsHavePosts;
    use Relationships\TagsHaveSections;

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
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * Tag constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->label = 'default';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $this->slugify($label);

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    private function setDefaults()
    {
        if (!$this->type) {
            $this->setType($this->name);
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