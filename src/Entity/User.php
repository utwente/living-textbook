<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;

/**
 * Class User
 *
 * @author BobV
 *
 * @ORM\Table(name="user__table", indexes={@ORM\Index(columns={"username"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @UniqueEntity("username", message="user.email-used", errorPath="username")
 *
 *
 * Authentication error order:
 * PreAuth:
 * - LockedException (isAccountNonLocked)          -> (inactive)
 * - DisabledException (isEnabled)                 -> active flag false
 * - AccountExpiredException (isAccountNonExpired) -> (inactive)
 * PostAuth
 * - CredentialsExpiredException (isCredentialsNonExpired) -> (inactive)
 */
class User implements AdvancedUserInterface, \Serializable
{

  use Blameable;
  use SoftDeletable;

  /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * Full name
   *
   * @var string
   *
   * @ORM\Column(name="first_name", type="string", length=100)
   *
   * @Assert\Length(min=2,max=100)
   */
  protected $firstName;

  /**
   * Full name
   *
   * @var string
   *
   * @ORM\Column(name="last_name", type="string", length=100)
   *
   * @Assert\Length(min=5,max=100)
   */
  protected $lastName;

  /**
   * Authentication name (username), equal to email address
   *
   * @var string
   *
   * @ORM\Column(name="username", type="string", length=255, unique=true)
   *
   * @Assert\NotBlank()
   * @Assert\Email()
   * @Assert\Length(min="5", max="255")
   */
  protected $username;

  /**
   * Password, stored encrypted
   *
   * @var string
   *
   * @ORM\Column(name="password", type="string", length=255, nullable=true)
   *
   * @Assert\Length(min=10, max=2048)
   */
  protected $password;

  /**
   * Datetime on which the user registered
   *
   * @var \DateTime
   *
   * @ORM\Column(name="registered_on", type="datetime")
   *
   * @Assert\NotNull()
   */
  protected $registeredOn;

  /**
   * Whether the account is active or not
   *
   * @var bool
   * @ORM\Column(name="is_active", type="boolean")
   */
  private $isActive;

  /**
   * @var array[string]
   *
   * @ORM\Column(name="roles", type="array", nullable=false, options={"default" = "a:0:{}"})
   *
   * @Assert\NotNull()
   */
  private $securityRoles;

  /**
   * User constructor.
   */
  public function __construct()
  {
    $this->registeredOn     = new \DateTime();
    $this->isActive         = true;
    $this->securityRoles    = array();
  }

  /**
   * Get the full name (firstname lastname)
   *
   * @return string
   */
  public function getFullName()
  {
    return sprintf('%s %s', $this->firstName, $this->lastName);
  }

  /**
   * Get the formatted to array for swift mailer
   *
   * @return array
   */
  public function getSwiftMailerTo()
  {
    return array($this->username => $this->getFullName());
  }

  /**
   * String representation of object
   *
   * @link  http://php.net/manual/en/serializable.serialize.php
   * @return string the string representation of the object or null
   * @since 5.1.0
   */
  public function serialize()
  {
    return serialize(array(
        $this->id,
        $this->username,
        $this->password,
        $this->isActive,
    ));
  }

  /**
   * Constructs the object
   *
   * @link  http://php.net/manual/en/serializable.unserialize.php
   *
   * @param string $serialized <p>
   *                           The string representation of the object.
   *                           </p>
   *
   * @return void
   * @since 5.1.0
   */
  public function unserialize($serialized)
  {
    list (
        $this->id,
        $this->username,
        $this->password,
        $this->isActive,
        ) = unserialize($serialized);
  }

  /**
   * Checks whether the user is locked.
   *
   * Internally, if this method returns false, the authentication system
   * will throw a LockedException and prevent login.
   *
   * @return bool true if the user is not locked, false otherwise
   *
   * @see LockedException
   */
  public function isAccountNonLocked()
  {
    // Account is locked when there is no password
    return $this->password !== NULL;
  }

  /**
   * Checks whether the user is enabled.
   *
   * Internally, if this method returns false, the authentication system
   * will throw a DisabledException and prevent login.
   *
   * @return bool true if the user is enabled, false otherwise
   *
   * @see DisabledException
   */
  public function isEnabled()
  {
    return $this->isActive;
  }

  /**
   * Checks whether the user's account has expired.
   *
   * Internally, if this method returns false, the authentication system
   * will throw an AccountExpiredException and prevent login.
   *
   * @return bool true if the user's account is non expired, false otherwise
   *
   * @see AccountExpiredException
   */
  public function isAccountNonExpired()
  {
    return true;
  }

  /**
   * Checks whether the user's credentials (password) has expired.
   *
   * Internally, if this method returns false, the authentication system
   * will throw a CredentialsExpiredException and prevent login.
   *
   * @return bool true if the user's credentials are non expired, false otherwise
   *
   * @see CredentialsExpiredException
   */
  public function isCredentialsNonExpired()
  {
    return true;
  }

  /**
   * Returns the roles granted to the user.
   *
   * <code>
   * public function getRoles()
   * {
   *     return array('ROLE_USER');
   * }
   * </code>
   *
   * Alternatively, the roles might be stored on a ``roles`` property,
   * and populated in any number of different ways when the user object
   * is created.
   *
   * @return Role[]|string[] The user roles
   */
  public function getRoles()
  {
    return array_merge(array('ROLE_USER'), $this->securityRoles);
  }

  /**
   * Returns the salt that was originally used to encode the password.
   *
   * This can return null if the password was not encoded using a salt.
   * This is the case as we use bcrypt
   *
   * @return string|null The salt
   */
  public function getSalt()
  {
    return NULL;
  }

  /**
   * Removes sensitive data from the user.
   *
   * This is important if, at any given point, sensitive information like
   * the plain-text password is stored on this object.
   */
  public function eraseCredentials()
  {
    // TODO: Implement eraseCredentials() method.
  }

  /**
   * @return mixed
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
  }

  /**
   * @param string $firstName
   *
   * @return $this
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;

    return $this;
  }

  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }

  /**
   * @param string $lastName
   *
   * @return User
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;

    return $this;
  }

  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * @param string $username
   *
   * @return $this
   */
  public function setUsername($username)
  {
    $this->username = $username;

    return $this;
  }

  /**
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }

  /**
   * @param string $password
   *
   * @return $this
   */
  public function setPassword($password)
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @return bool
   */
  public function isIsActive()
  {
    return $this->isActive;
  }

  /**
   * @param bool $isActive
   *
   * @return User
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;

    return $this;
  }

  /**
   * Set registeredOn
   *
   * @param \DateTime $registeredOn
   *
   * @return User
   */
  public function setRegisteredOn($registeredOn)
  {
    $this->registeredOn = $registeredOn;

    return $this;
  }

  /**
   * Get registeredOn
   *
   * @return \DateTime
   */
  public function getRegisteredOn()
  {
    return $this->registeredOn;
  }

  /**
   * Get isActive
   *
   * @return boolean
   */
  public function getIsActive()
  {
    return $this->isActive;
  }

  /**
   * Generate a random secure token
   *
   * @return string
   * @throws \Exception
   */
  public function generateToken()
  {
    return bin2hex(random_bytes(40));
  }

  /**
   * Set securityRoles
   *
   * @param array $securityRoles
   *
   * @return User
   */
  public function setSecurityRoles($securityRoles)
  {
    sort($securityRoles);
    $this->securityRoles = $securityRoles;

    return $this;
  }

  /**
   * Get securityRoles
   *
   * @return array
   */
  public function getSecurityRoles()
  {
    return $this->securityRoles;
  }

}
