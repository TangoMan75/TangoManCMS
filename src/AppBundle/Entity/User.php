<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="user")
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà utilisé.")
 * @UniqueEntity(fields={"username"}, message="Ce nom d'utilisateur est déjà utilisé.")
 *
 * @author  Matthias Morin <tangoman@free.fr>
 * @package AppBundle\Entity
 */
class User implements UserInterface
{
    use Relationships\UserHasComments;
    use Relationships\UserHasPosts;
    use Relationships\UserHasVotes;
    use Relationships\UsersHavePrivileges;
    use Relationships\UsersHaveRoles;

    use Traits\Privatable;
    use Traits\Sluggable;
    use Traits\Timestampable;

    /**
     * @var int User id
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

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
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Expression("value not in ['delete','edit','new','register','unsubscribe']", message="Désolé, ce nom d'utilisateur est réservé.")
     * @Assert\NotBlank(message="Merci de renseigner un nom d'utilisateur.")
     */
    private $username;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="Votre email doit être valide.")
     * @Assert\NotBlank(message="Merci de renseigner votre adresse email.")
     */
    private $email;

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
     * @var string User's password hash
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min=8, minMessage="Votre mot de passe doit contenir au moins {{ limit }} caractères.")
     * @Assert\Regex(
     *     pattern="/(?=.*\d)(?=.*[a-z])(?=.*[A-Z])/",
     *     message="Votre mot de passe doit au moins contenir une minuscule, une majuscule et un chiffre."
     * )
     */
    private $password;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTimeImmutable();
        $this->modified = new \DateTimeImmutable();
        $this->posts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->roles = new ArrayCollection();
        $this->privileges = new ArrayCollection();
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
        if (!$this->postCount) {
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
        if (!$this->mediaCount) {
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

    /**
     * These methods are optional but strongly recommended
     */

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * these methods are required by Symfony\Component\Security\Core\User\UserInterface
     */

    /**
     * Set username and slug.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        if (!$this->slug) {
            $this->setUniqueSlug($this->username);
        }

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Erase user's credentials.
     * Abstract method required by symfony core
     */
    public function eraseCredentials()
    {
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Get salt
     * Abstract method required by symfony core
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Default settings
     */

    /**
     * @ORM\PreFlush()
     */
    public function setDefaults()
    {
        if (!$this->slug) {
            $this->setUniqueSlug($this->username);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }
}
