<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Site
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SiteRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="site")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Site
{
    use Relationships\SitesHavePages;
    use Relationships\SitesHaveTags;

    use Traits\HasSummary;
    use Traits\HasTitle;
    use Traits\HasViews;
    use Traits\Publishable;
    use Traits\Sluggable;
    use Traits\Timestampable;

    /**
     * @var int Site id
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $pageCount;

    /**
     * Site constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->pages = new ArrayCollection();
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
     * @return int
     */
    public function getPageCount()
    {
        if (!$this->pageCount) {
            return 0;
        }

        return $this->pageCount;
    }

    /**
     * @param $pageCount
     *
     * @return $this
     */
    public function setPageCount($pageCount)
    {
        $this->pageCount = $pageCount;

        return $this;
    }

    /**
     * @ORM\PreFlush()
     */
    public function setDefaults()
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
