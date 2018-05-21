<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Oidc\Exception\OidcException;
use App\Oidc\Security\Authentication\Token\OidcToken;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

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
 * @UniqueEntity({"username", "isOidc"}, message="user.email-used", errorPath="username")
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

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * Given name
   *
   * @var string
   *
   * @ORM\Column(name="given_name", type="string", length=100)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=2,max=100)
   */
  protected $givenName;

  /**
   * Family name
   *
   * @var string
   *
   * @ORM\Column(name="last_name", type="string", length=100)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=2,max=100)
   */
  protected $familyName;

  /**
   * Full name
   *
   * @var string
   *
   * @ORM\Column(name="full_name", type="string", length=200)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=4, max=200)
   */
  protected $fullName;

  /**
   * Display name
   *
   * @var string
   *
   * @ORM\Column(name="display_name", type="string", length=200)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=4, max=200)
   */
  protected $displayName;

  /**
   * Authentication name (username), equal to email address
   *
   * @var string
   *
   * @ORM\Column(name="username", type="string", length=255)
   *
   * @Assert\NotBlank()
   * @Assert\Email()
   * @Assert\Length(min=5, max=255)
   */
  protected $username;

  /**
   * If set, the account was created using OIDC
   *
   * @var boolean
   *
   * @ORM\Column(name="is_oidc", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   */
  protected $isOidc = false;

  /**
   * Password, stored encrypted, if any.
   * No password is stored for OIDC authentication
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
   * DateTime on which the user has lastly logged on
   *
   * @var \DateTime|null
   *
   * @ORM\Column(name="last_used", type="datetime")
   */
  protected $lastUsed;

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
   * @ORM\Column(name="roles", type="array", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $securityRoles;

  /**
   * @var UserGroup[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\UserGroup", mappedBy="users")
   */
  private $userGroups;

  /**
   * User constructor.
   */
  public function __construct()
  {
    $this->registeredOn  = new \DateTime();
    $this->isActive      = true;
    $this->securityRoles = array();
    $this->userGroups    = new ArrayCollection();
  }

  public function __toString(): string
  {
    return $this->getDisplayName() . ' (' . $this->getUsername() . ')';
  }

  /**
   * Create a user from a token object
   *
   * @param OidcToken $token
   *
   * @return User
   * @throws OidcException
   */
  public static function createFromToken(OidcToken $token): User
  {
    $username = $token->getUsername();

    // Username must not be empty!
    if (empty($username)) {
      throw new OidcException('Retrieved username from OIDC is empty!');
    }

    return (new User())
        ->setUsername($username)
        ->setIsOidc(true)
        ->update($token);
  }

  /**
   * Update the user with the latest information from the token
   * Do not update the username, this should be the same anyways
   *
   * @param OidcToken $token
   *
   * @return User
   */
  public function update(OidcToken $token): User
  {
    return $this
        ->setDisplayName($token->getDisplayName())
        ->setFullName($token->getFullName())
        ->setGivenName($token->getGivenName())
        ->setFamilyName($token->getFamilyName());
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
        $this->isOidc,
    ));
  }

  /**
   * Constructs the object
   *
   * @link  http://php.net/manual/en/serializable.unserialize.php
   *
   * @param string $serialized The string representation of the object.
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
        $this->isOidc,
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
    return true;
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
    $this->password = NULL;
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
   * @return bool
   */
  public function isOidc(): bool
  {
    return $this->isOidc;
  }

  /**
   * @param bool $isOidc
   *
   * @return User
   */
  public function setIsOidc(bool $isOidc): User
  {
    $this->isOidc = $isOidc;

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
   * Get isActive
   *
   * @return boolean
   */
  public function getIsActive()
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
   * Get registeredOn
   *
   * @return \DateTime
   */
  public function getRegisteredOn()
  {
    return $this->registeredOn;
  }

  /**
   * @return \DateTime|null
   */
  public function getLastUsed(): ?\DateTime
  {
    return $this->lastUsed;
  }

  /**
   * @param \DateTime $lastUsed
   *
   * @return User
   */
  public function setLastUsed(\DateTime $lastUsed): User
  {
    $this->lastUsed = $lastUsed;

    return $this;
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

  /**
   * @return string
   */
  public function getGivenName(): string
  {
    return $this->givenName;
  }

  /**
   * @param string $givenName
   *
   * @return User
   */
  public function setGivenName(string $givenName): User
  {
    $this->givenName = $givenName;

    return $this;
  }

  /**
   * @return string
   */
  public function getFamilyName(): string
  {
    return $this->familyName;
  }

  /**
   * @param string $familyName
   *
   * @return User
   */
  public function setFamilyName(string $familyName): User
  {
    $this->familyName = $familyName;

    return $this;
  }

  /**
   * @return string
   */
  public function getFullName(): string
  {
    return $this->fullName;
  }

  /**
   * @param string $fullName
   *
   * @return User
   */
  public function setFullName(string $fullName): User
  {
    $this->fullName = $fullName;

    return $this;
  }

  /**
   * @return string
   */
  public function getDisplayName(): string
  {
    return $this->displayName;
  }

  /**
   * @param string $displayName
   *
   * @return User
   */
  public function setDisplayName(string $displayName): User
  {
    $this->displayName = $displayName;

    return $this;
  }

  /**
   * @return UserGroup[]|Collection
   */
  public function getUserGroups(){
    return $this->userGroups;
  }

}
