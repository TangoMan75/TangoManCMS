<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\Sluggable;
use AppBundle\Entity\Traits\Timestampable;
use AppBundle\Entity\Traits\Taggable;
use AppBundle\Entity\Traits\Publishable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Page
{
    use Sluggable;

    use Timestampable;

    use Taggable;

    use Publishable;

    /**
     * @var int Page id
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string Title
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre doit être renseigné")
     */
    private $title;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="page")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $items;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        $this->items = new ArrayCollection();
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
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        // Sets slug when empty
        $this->title = $title;
        if (!$this->slug) {
            $this->setUniqueSlug($title);
        }

        return $this;
    }

    /**
     * Get items
     *
     * @return Page[]|Post[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add item
     *
     * @return Page
     */
    public function addItem($item)
    {
        if (!in_array($item, $this->items)) {
            $this->items[] = $item;
        }

        return $this;
    }

    /**
     * Remove item
     *
     * @param $item
     *
     * @return Page
     */
    public function removeItem($item)
    {
        $this->items->removeElement($item);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }
}
