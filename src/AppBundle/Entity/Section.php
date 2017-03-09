<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="section")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SectionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Section
{
    use Publishable;

    /**
     * @var int
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Page
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Page", inversedBy="sections")
     */
    private $page;

    /**
     * Section constructor.
     */
    public function __construct()
    {
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
     * Set page
     *
     * @param Page $page
     *
     * @return Section
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }
}
