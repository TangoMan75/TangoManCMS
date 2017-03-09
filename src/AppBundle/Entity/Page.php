<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="page")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Page
{
    use Slugable;

    use Publishable;

    /**
     * @var int
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string Description
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var Section[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Section", mappedBy="pages", cascade={"remove"})
     * @ORM\Column(name="sections", type="simple_array", nullable=true)
     */
    private $sections;

    /**
     * Page constructor.
     */
    public function __construct()
    {
        $this->sections = [];
    }

    /**
     * Set id
     *
     * @param string $id
     *
     * @return Page
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set sections
     *
     * @param array $sections
     *
     * @return Page
     */
    public function setSections($sections)
    {
        $this->sections = $sections;

        return $this;
    }

    /**
     * Get sections
     *
     * @return array
     */
    public function getSections()
    {
        return $this->sections;
    }
}
