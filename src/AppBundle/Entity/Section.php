<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Section
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Section
{
    use Relationships\SectionHasPages;
    use Relationships\SectionHasPosts;
    use Relationships\Taggable;

    use Traits\HasSummary;
    use Traits\HasTitle;
    use Traits\HasType;
    use Traits\Publishable;
    use Traits\Sluggable;
    use Traits\Timestampable;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre doit être renseigné")
     */
    private $title;

    /**
     * @var array|Post[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="sections", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts = [];

    /**
     * @var array|Page[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", inversedBy="sections", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $pages = [];

    /**
     * Section constructor.
     */
    public function __construct()
    {
        $this->type = 'default';
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->posts = new ArrayCollection();
        $this->pages = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    private function setDefaults()
    {
        if (!$this->title) {
            $this->setTitle($this->created->format('d/m/Y H:i:s'));
        }

        if (!$this->slug) {
            $this->setUniqueSlug($this->title);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
