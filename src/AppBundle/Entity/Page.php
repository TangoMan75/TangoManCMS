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

    /**
     * @var int Page id
     * @ORM\Id
     * @ORM\Column(type="integer", unique=true)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published = false;

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
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Media", mappedBy="page")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $listMedia;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        $this->posts = new ArrayCollection();
        $this->listMedia = new ArrayCollection();
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
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     *
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
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
     * Get posts
     *
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add post
     *
     * @param $post
     *
     * @return $this
     */
    public function addPost($post)
    {
        if (!in_array($post, $this->posts)) {
            $this->posts[] = $post;
        }

        return $this;
    }

    /**
     * Remove post
     *
     * @param $post
     *
     * @return $this
     */
    public function removePost($post)
    {
        $this->posts->removeElement($post);

        return $this;
    }

    /**
     * Get listMedia
     *
     * @return ArrayCollection
     */
    public function getListMedia()
    {
        return $this->listMedia;
    }

    /**
     * Add media
     *
     * @param $media
     *
     * @return $this
     */
    public function addMedia($media)
    {
        if (!in_array($media, $this->listMedia)) {
            $this->listMedia[] = $media;
        }

        return $this;
    }

    /**
     * Remove media
     *
     * @param $media
     *
     * @return $this
     */
    public function removeMedia($media)
    {
        $this->listMedia->removeElement($media);

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
