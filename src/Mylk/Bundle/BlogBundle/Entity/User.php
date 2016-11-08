<?php

namespace Mylk\Bundle\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements AdvancedUserInterface, \Serializable
{
    public function __construct()
    {
        $this->isActive = true;
        $this->roles = new ArrayCollection();
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=100, nullable=false, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(name="last_login", type="string", length=20, nullable=false)
     */
    protected $lastLogin;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="users_roles")
     */
    protected $roles;

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        // create a password hash with bcrypt as defined in security.yml, security.encoders
        $this->password = password_hash($password, PASSWORD_BCRYPT, array("cost" => 12));

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function setLastLogin($lastLogin)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles->toArray();
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    // methods required by AdvancedUserInterface
    public function getSalt()
    {
        return null;
    }

    public function eraseCredentials()
    {
        
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }

    // methods used to serialize/unserialize user entities into/from serssion
    public function serialize()
    {
        return \serialize(array(
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            $this->isActive
        ) = \unserialize($serialized);
    }
}
