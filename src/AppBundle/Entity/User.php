<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TangoMan\EntityHelper\Traits\Privatable;
use TangoMan\RelationshipBundle\Traits\HasRelationships;
use TangoMan\RoleBundle\Relationships\UsersHavePrivileges;
use TangoMan\RoleBundle\Relationships\UsersHaveRoles;
use TangoMan\UserBundle\Model\User as TangoManUser;

/**
 * Class User
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 * @author  Matthias Morin <matthias.morin@gmail.com>
 * @package AppBundle\Entity
 */
class User extends TangoManUser
{

    use HasRelationships;
    use Privatable;
    use UsersHavePrivileges;
    use UsersHaveRoles;

    /**
     * @var Comment[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user",
     *                                                         cascade={"persist",
     *                                                         "remove"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $comments;

    /**
     * @var Post[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user",
     *                                                      cascade={"persist",
     *                                                      "remove"})
     * @ORM\OrderBy({"modified"="DESC"})
     */
    private $posts;

    /**
     * @var Vote[]
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Vote", mappedBy="user",
     *                                                      cascade={"persist",
     *                                                      "remove"})
     */
    private $votes;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $postCount;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mediaCount;

    /**
     * @var string Base64 avatar image
     * @ORM\Column(type="text", nullable=true)
     */
    private $avatar;

    /**
     * @var string Biography
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->roles      = new ArrayCollection();
        $this->privileges = new ArrayCollection();
        $this->posts      = new ArrayCollection();
        $this->comments   = new ArrayCollection();
        $this->votes      = new ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPostCount()
    {
        if ( ! $this->postCount) {
            return 0;
        }

        return $this->postCount;
    }

    /**
     * @param int $postCount
     *
     * @return $this
     */
    public function setPostCount($postCount)
    {
        $this->postCount = $postCount;

        return $this;
    }

    /**
     * @return int
     */
    public function getMediaCount()
    {
        if ( ! $this->mediaCount) {
            return 0;
        }

        return $this->mediaCount;
    }

    /**
     * @param $mediaCount
     *
     * @return $this
     */
    public function setMediaCount($mediaCount)
    {
        $this->mediaCount = $mediaCount;

        return $this;
    }

    /**
     * Get user's avatar (Base64).
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set user's avatar (Base64).
     *
     * @param string $avatar
     *
     * @return $this
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get user's bio
     *
     * @return String
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * Set user's bio
     *
     * @param String $bio
     *
     * @return $this
     */
    public function setBio($bio)
    {
        $this->bio = $bio;

        return $this;
    }

    /**
     * Get user's posts.
     *
     * @return Post[]|ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Sets user posts.
     *
     * @param Post[] $posts
     *
     * @return $this
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;

        return $this;
    }

    /**
     * Get user's comments.
     *
     * @return Comment[]|ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Set user's comments.
     *
     * @param Comment[] $comments
     *
     * @return $this
     */
    public function setComments($comments)
    {
        $this->comments = $comments;

        return $this;
    }
}
