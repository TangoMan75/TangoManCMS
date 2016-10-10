<?php
/**
 * Created by PhpStorm.
 * User: MORIN Matthias
 * Date: 01/10/2016
 * Time: 17:09
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="Ce nom d'utilisateur est déjà utilisé.")
 * @UniqueEntity(fields={"email"}, message="Cet email est déjà utilisé.")
 */
class User implements UserInterface
{
    /**
     * @var int User id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string User's name
     *
     * @ORM\Column(name="username", type="string", length=255)
     * @Assert\Expression("value not in ['confirm', 'delete', 'edit', 'export', 'import', 'register']", message="Ce nom d'utilisateur est réservé.")
     * @Assert\NotBlank(message="Vous devez renseigner un nom d'utilisateur.")
     */
    private $username;

    /**
     * @var string User's email address
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Email(checkMX=true, message="Votre email doit être valide.")
     * @Assert\NotBlank(message="Vous devez renseigner votre adresse email.")
     */
    private $email;

    /**
     * @var string User's password hash
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @var string User's security token
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @var object User's posts
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"dateCreated"="DESC"})
     */
    private $posts;

    /**
     * @var array User's roles
     *
     * @ORM\Column(name="roles", type="simple_array")
     */
    private $roles;

    /**
     * @var \DateTime User's registration date
     *
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    public function __construct()
    {
        $this->roles = ["ROLE_USER"];
        $this->dateCreated = new \DateTime();
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
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

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
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Set token.
     *
     * @param string $token
     *
     * @return User
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
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
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles.
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
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
        if ( !in_array($role, $this->roles) ) {
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

        if ( in_array($role, $roles) ) {
            $remove[] = $role;
            $this->roles = array_diff($roles, $remove);
        }

        return $this;
    }

    /**
     * Erase user's credentials.
     *
     * Abstract method required by symfony core
     */
    public function eraseCredentials()
    {
    }

    /**
     * Get user's posts.
     *
     * @return Post
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Get salt
     *
     * Abstract method required by symfony core
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Sets user posts.
     *
     * @param User $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * Get user's creation.
     *
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Sets user's creation date.
     *
     * @param mixed $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

}
