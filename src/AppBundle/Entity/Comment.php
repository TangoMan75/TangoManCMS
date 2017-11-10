<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use TangoMan\EntityHelper\Traits\Publishable;
use TangoMan\EntityHelper\Traits\Timestampable;
use TangoMan\RelationshipBundle\Traits\HasRelationships;

/**
 * Class Comment
 * @ORM\Table(name="comment")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>
 * @package AppBundle\Entity
 *
 * @method $this setUser(User $user)
 */
class Comment
{
    use HasRelationships;
    use Publishable;
    use Timestampable;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Post
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Post", inversedBy="comments", cascade={"persist"})
     */
    private $post;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="comments", cascade={"persist"})
     */
    private $user;

    /**
     * @var string
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
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
        return substr(strip_tags($this->text), 0, 50);
    }
}
