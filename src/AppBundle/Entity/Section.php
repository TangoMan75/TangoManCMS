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
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Post", mappedBy="sections")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts = [];

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Page", mappedBy="sections")
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $pages = [];

    /**
     * Post constructor.
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
        if (!in_array($post, $this->posts)) {
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
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function addPage($page)
    {
        if (!in_array($page, $this->pages)) {
            $this->pages[] = $page;
        }

        return $this;
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function removePage($page)
    {
        $this->pages->removeElement($page);

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
