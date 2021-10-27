<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Drenso\OidcBundle\Exception\OidcException;
use Drenso\OidcBundle\Security\Authentication\Token\OidcToken;
use Gedmo\Mapping\Annotation as Gedmo;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class User
 *
 * @author BobV
 *
 * @ORM\Table(name="user__table", indexes={@ORM\Index(columns={"username"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\EntityListeners({"App\Entity\Listener\UserListener"})
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @UniqueEntity({"username", "isOidc"}, message="user.email-used", errorPath="username")
 */
class User implements UserInterface, Serializable
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
   * @Groups({"user:read", "user:write"})
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
   * @Groups({"user:read", "user:write"})
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
   * @Groups({"user:read", "user:write"})
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
   * @ORM\Column(name="username", type="string", length=180)
   *
   * @Assert\NotBlank()
   * @Assert\Email()
   * @Assert\Length(min=5, max=180)
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
   */
  protected $password;

  /**
   * Datetime on which the user registered
   *
   * @var DateTime
   *
   * @ORM\Column(name="registered_on", type="datetime")
   *
   * @Assert\NotNull()
   */
  protected $registeredOn;

  /**
   * DateTime on which the user has lastly logged on
   *
   * @var DateTime|null
   *
   * @ORM\Column(name="last_used", type="datetime", nullable=true)
   */
  protected $lastUsed;

  /**
   * @var array[string]
   *
   * @ORM\Column(name="roles", type="array", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $securityRoles;

  /**
   * @var boolean
   *
   * @ORM\Column(name="is_admin", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Type("bool")
   */
  private $isAdmin;

  /**
   * Hashed reset code, used for resetting the password
   *
   * @var string|null
   *
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private $resetCode;

  /**
   * Datetime till when the reset code is still valid
   *
   * @var DateTime|null
   *
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $resetCodeValid;

  /**
   * @var UserGroup[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\UserGroup", mappedBy="users")
   */
  private $userGroups;

  /**
   * @var Annotation[]|Collection
   *
   * @ORM\OneToMany(targetEntity="Annotation", mappedBy="user")
   */
  private $annotations;

  /**
   * User constructor.
   */
  public function __construct()
  {
    $this->givenName     = '';
    $this->familyName    = '';
    $this->fullName      = '';
    $this->registeredOn  = new DateTime();
    $this->isOidc        = false;
    $this->isAdmin       = false;
    $this->securityRoles = array();
    $this->userGroups    = new ArrayCollection();
    $this->annotations   = new ArrayCollection();
  }

  /**
   * String representation to be used in selection list
   *
   * @return string
   */
  public function selectionName(): string
  {
    if ($this->isOidc()) {
      return $this->getDisplayName();
    }

    return $this->getDisplayName() . ' (Local account)';
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
   * Custom sorter, based on display name
   *
   * @param User $a
   * @param User $b
   *
   * @return int
   */
  public static function sortOnDisplayName(User $a, User $b): int
  {
    return strcmp($a->getDisplayName(), $b->getDisplayName());
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
   * Get the mailer Address for this User
   *
   * @return Address
   */
  public function getAddress()
  {
    return new Address($this->username, $this->getFullName());
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
      true, // BC-compatibility with serialized sessions
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
    list(
      $this->id,
      $this->username,
      $this->password,, //  BC-compatibility with serialized sessions
      $this->isOidc,
    ) = unserialize($serialized);
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
    $roles = ['ROLE_USER'];

    if ($this->isAdmin()) {
      $roles[] = 'ROLE_SUPER_ADMIN';
    }

    return array_merge($roles, $this->securityRoles);
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
    $this->username = mb_strtolower($username);

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
   * Get registeredOn
   *
   * @return DateTime
   */
  public function getRegisteredOn()
  {
    return $this->registeredOn;
  }

  /**
   * @return DateTime|null
   */
  public function getLastUsed(): ?DateTime
  {
    return $this->lastUsed;
  }

  /**
   * @param DateTime $lastUsed
   *
   * @return User
   */
  public function setLastUsed(DateTime $lastUsed): User
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
   * @return bool
   */
  public function isAdmin(): bool
  {
    return $this->isAdmin;
  }

  /**
   * @param bool $isAdmin
   *
   * @return User
   */
  public function setIsAdmin(bool $isAdmin): User
  {
    $this->isAdmin = $isAdmin;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getResetCode(): ?string
  {
    return $this->resetCode;
  }

  /**
   * @param string|null $resetCode
   *
   * @return User
   */
  public function setResetCode(?string $resetCode): self
  {
    $this->resetCode = $resetCode;

    return $this;
  }

  /**
   * @return DateTime|null
   */
  public function getResetCodeValid(): ?DateTime
  {
    return $this->resetCodeValid;
  }

  /**
   * @param DateTime|null $resetCodeValid
   *
   * @return User
   */
  public function setResetCodeValid(?DateTime $resetCodeValid): self
  {
    $this->resetCodeValid = $resetCodeValid;

    return $this;
  }

  /**
   * @return UserGroup[]|Collection
   */
  public function getUserGroups()
  {
    return $this->userGroups;
  }

  /**
   * @return Annotation[]|Collection
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
}
