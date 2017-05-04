<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Section
{
    use Traits\Sluggable;
    use Traits\Timestampable;
    use Traits\Taggable;

    /**
     * @var int
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
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre doit être renseigné")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="section")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Media", mappedBy="section")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $listMedia;

    /**
     * Post constructor.
     */
    public function __construct()
    {
        $this->type = 'default';
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->posts = new ArrayCollection();
        $this->listMedia = new ArrayCollection();
    }

    /**
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Section
     */
    public function setType($type)
    {
        $this->type = $type;

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
     * @param $post
     *
     * @return $this
     */
    public function addPost($post)
    {
        if (!in_array($post, (array)$this->posts)) {
            $this->posts[] = $post;
        }

        return $this;
    }

    /**
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
     * @return ArrayCollection
     */
    public function getListMedia()
    {
        return $this->listMedia;
    }

    /**
     * @param $media
     *
     * @return $this
     */
    public function addMedia($media)
    {
        if (!in_array($media, (array)$this->listMedia)) {
            $this->listMedia[] = $media;
        }

        return $this;
    }

    /**
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
