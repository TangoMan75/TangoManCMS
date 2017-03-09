<?php

namespace AppBundle\Entity;

Trait Publishable
{
    /**
     * @var string Title
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le titre doit être renseigné")
     */
    private $title;

    /**
     * @var Tag[]
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Tag", inversedBy="items")
     */
    private $tags;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $published;

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
     * @return boolean
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param boolean $published
     * @return $this
     */
    public function setPublished($published)
    {
        $this->published = $published;

        return $this;
    }

    /**
     * @return Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param Tag $tag
     *
     * @return $this
     */
    public function addTag(Tag $tag)
    {
        // Only one of each is allowed
        if (!in_array($tag, $this->tags)) {
            array_push($this->tags, $tag);
        }

        return $this;
    }

    /**
     * @param tag $tag
     *
     * @return $this
     */
    public function removeTag(tag $tag)
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}