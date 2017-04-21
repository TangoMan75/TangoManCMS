<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Traits\Publishable;
use AppBundle\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment
{
    use Timestampable;

    use Publishable;

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
     * @var string Message text
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Votre message ne peut pas être vide")
     */
    private $text;

    /**
     * Comment constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
    }

    /**
     * @return int Comment id
     */
    public function getId()
    {
        return $this->id;
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

    /**
     * @return string Comment text
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text Comment text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return substr(strip_tags($this->text), 0, 20).'...';
    }
}
