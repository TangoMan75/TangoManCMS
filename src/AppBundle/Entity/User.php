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
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     * @Assert\Expression("value not in ['edit', 'register', 'confirm', 'delete']", message="Ce nom d'utilisateur est réservé.")
     * @Assert\NotBlank(message="Vous devez renseigner un nom d'utilisateur.")
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\Email(checkMX=true, message="Votre email doit être valide.")
     * @Assert\NotBlank(message="Vous devez renseigner votre adresse email.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var User's posts
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Post", mappedBy="user", cascade={"remove"})
     * @ORM\OrderBy({"dateCreated"="DESC"})
     */
    private $posts;

    /**
     * @var string
     *
     * @ORM\Column(name="roles", type="simple_array")
     */
    private $roles;

    /**
     * @var \DateTime Post date
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    public function __construct() {
        $this->roles = ["ROLE_USER"];
        $this->token = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->dateCreated = new \DateTime();
        
//        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
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
     * Set username
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
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set email
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
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
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
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set token
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
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set role
     *
     * @param array $role
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Get salt
     *
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Erase credentials
     */
    public function eraseCredentials()
    {
    }

    /**
     * @return User
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param User $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @return mixed
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param mixed $dateCreated
     */
    public function setDateCreated(\DateTime $dateCreated)
    {
        $this->dateCreated = $dateCreated;
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

}
