<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @var integer Comment id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User Comment author
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments")
     */
    private $user;

    /**
     * @var Post Post id
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="comments")
     */
    private $post;

    /**
     * @var \DateTime Comment date
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @var string Message content
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Votre message ne peut pas être vide")
     */
    private $content;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->dateCreated = new \DateTime();
    }

    /**
     * @return int Comment id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return string Comment content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content Comment content
     *
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return User Comment author
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user Comment author
     *
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Post Comment's post
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * @param Post $post Comment's post
     *
     * @return $this
     */
    public function setPost($post)
    {
        $this->post = $post;

        return $this;
    }

}
