<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Section
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="section")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Section
{
    use Relationships\SectionsHavePages;
    use Relationships\SectionsHavePosts;
    use Relationships\SectionsHaveTags;

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
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mediaCount;

    /**
     * Section constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->posts = new ArrayCollection();
        $this->pages = new ArrayCollection();
        $this->tags = new ArrayCollection();

        $this->type = 'default';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPostCount()
    {
        if (!$this->postCount) {
            return 0;
        }

        return $this->postCount;
    }

    /**
     * @param int $postCount
     *
     * @return $this
     */
    public function setPostCount($postCount)
    {
        $this->postCount = $postCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMediaCount()
    {
        if (!$this->mediaCount) {
            return 0;
        }

        return $this->mediaCount;
    }

    /**
     * @param $mediaCount
     *
     * @return $this
     */
    public function setMediaCount($mediaCount)
    {
        $this->mediaCount = $mediaCount;

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
