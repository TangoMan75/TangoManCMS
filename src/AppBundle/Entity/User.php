<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Ce nom d'utilisateur est déjà utilisé.")
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà utilisé.")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    use Slugable;

    use Timestampable;

    /**
     * @var int User id
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string User's name
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Expression("value not in ['delete','edit','register','unsubscribe']", message="Désolé, ce nom d'utilisateur est réservé.")
     * @Assert\NotBlank(message="Merci de renseigner un nom d'utilisateur.")
     */
    private $username;

    /**
     * @var string User's email address
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="Votre email doit être valide.")
     * @Assert\NotBlank(message="Merci de renseigner votre adresse email.")
     */
    private $email;

    /**
     * @var string Base64 user avatar image
     * @ORM\Column(type="text", nullable=true)
     */
    private $avatar;

    /**
     * @var String User biography
     * @ORM\Column(type="text", nullable=true)
     */
    private $bio;

    /**
     * @var Post[] User's posts
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"created"="DESC"})
     */
    private $posts;

    /**
     * @var Comment[] User's comments
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"created"="DESC"})
     */
    private $comments;

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
     * @var array User roles
     * @ORM\Column(type="simple_array")
     */
    private $roles;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->created = new \DateTime();
        $this->modified = new \DateTime();
        $this->posts = [];
        $this->comments = [];
        $this->roles = ['ROLE_USER'];
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
     * @return Post[]
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
     * @return Comment[] User comment
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
     * Get roles.
     *
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set roles.
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        foreach ($roles as $role) {
            if (!in_array($role, $this->roles)) {
                array_push($this->roles, $role);
            }
        }

        $this->roles = $roles;

        return $this;
    }

    /**
     * Add role.
     *
     * @param string $role
     *
     * @return User
     */
    public function addRole($role)
    {
        $hierarchy = ['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_SUPER_USER', 'ROLE_USER'];

        foreach ($hierarchy as $key => $value) {
            if ($role == $value) {
                for ($i = $key; $i < count($hierarchy); $i++) {
                    if (!in_array($hierarchy[$i], $this->roles)) {
                        array_push($this->roles, $hierarchy[$i]);
                    }
                }

                return $this;
            }
        }

        if (!in_array($role, $this->roles)) {
            array_push($this->roles, $role);
        }

        return $this;
    }

    /**
     * Remove role.
     *
     * @param string $role
     *
     * @return User
     */
    public function removeRole($role)
    {
        $roles = $this->roles;
        if (in_array($role, $roles)) {
            $remove[] = $role;
            $this->roles = array_diff($roles, $remove);
        }

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

        // Sets Slug when empty
        if (!$this->slug) {
            $this->setUniqueSlug($username);
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

}
