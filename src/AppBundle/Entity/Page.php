<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Page
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Page
{
    use Relationships\PageHasSections;
    use Relationships\PageHasStats;
    use Relationships\PagesHaveTags;

    use Traits\HasSummary;
    use Traits\HasTitle;
    use Traits\Publishable;
    use Traits\Sluggable;
    use Traits\Timestampable;

    /**
     * @var int Page id
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Section[]|ArrayCollection
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Section", inversedBy="pages", cascade={"persist"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $sections = [];

    /**
     * Section constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->sections = new ArrayCollection();
    }

    /**
     * Get id
     *
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
