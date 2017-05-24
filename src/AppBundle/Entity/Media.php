<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class Media
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 *
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="media")
 * @Vich\Uploadable
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class Media
{
    use Relationships\MediaHasComments;
    use Relationships\MediaHasStats;
    use Relationships\MediasHaveSections;
    use Relationships\MediasHaveTags;
    use Relationships\MediasHaveUser;

    use Traits\Categorized;
    use Traits\Embeddable;
    use Traits\HasSummary;
    use Traits\HasText;
    use Traits\HasTitle;
    use Traits\HasType;
    use Traits\Publishable;
    use Traits\Sluggable;
    use Traits\Timestampable;
    use Traits\UploadableDocument;
    use Traits\UploadableImage;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Media constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->sections = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->stats = new ArrayCollection();
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