<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\OidcBundle\Exception\OidcException;
use Drenso\OidcBundle\Model\OidcUserData;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Override;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Mime\Address;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="user__table", indexes={@ORM\Index(columns={"username"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 * @ORM\EntityListeners({"App\Entity\Listener\UserListener"})
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @UniqueEntity({"username", "isOidc"}, message="user.email-used", errorPath="username")
 */
class User implements UserInterface, Serializable, PasswordAuthenticatedUserInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * Given name.
   *
   * @ORM\Column(name="given_name", type="string", length=100)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Length(min=2,max=100)
   */
  protected string $givenName = '';

  /**
   * Family name.
   *
   * @ORM\Column(name="last_name", type="string", length=100)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Length(min=2,max=100)
   */
  protected string $familyName = '';

  /**
   * Full name.
   *
   * @ORM\Column(name="full_name", type="string", length=200)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Length(min=4, max=200)
   */
  protected string $fullName = '';

  /**
   * Display name.
   *
   * @ORM\Column(name="display_name", type="string", length=200)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Length(min=4, max=200)
   */
  protected ?string $displayName = null;

  /**
   * Authentication name (username), equal to email address.
   *
   * @ORM\Column(name="username", type="string", length=180)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Email()
   *
   * @Assert\Length(min=5, max=180)
   */
  protected ?string $username = null;

  /**
   * If set, the account was created using OIDC.
   *
   * @ORM\Column(name="is_oidc", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   */
  protected bool $isOidc = false;

  /**
   * Password, stored encrypted, if any.
   * No password is stored for OIDC authentication.
   *
   * @ORM\Column(name="password", type="string", length=255, nullable=true)
   */
  protected ?string $password = null;

  /**
   * Datetime on which the user registered.
   *
   * @ORM\Column(name="registered_on", type="datetime")
   *
   * @Assert\NotNull()
   */
  protected DateTime $registeredOn;

  /**
   * DateTime on which the user has lastly logged on.
   *
   * @ORM\Column(name="last_used", type="datetime", nullable=true)
   */
  protected ?DateTime $lastUsed = null;

  /**
   * @var array[string]
   *
   * @ORM\Column(name="roles", type="array", nullable=false)
   *
   * @Assert\NotNull()
   */
  private array $securityRoles = [];

  /**
   * @ORM\Column(name="is_admin", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @Assert\Type("bool")
   */
  private bool $isAdmin = false;

  /**
   * Hashed reset code, used for resetting the password.
   *
   * @ORM\Column(type="string", length=255, nullable=true)
   */
  private ?string $resetCode = null;

  /**
   * Datetime till when the reset code is still valid.
   *
   * @ORM\Column(type="datetime", nullable=true)
   */
  private ?DateTime $resetCodeValid = null;

  /**
   * @var Collection<UserGroup>
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\UserGroup", mappedBy="users")
   */
  private Collection $userGroups;

  /**
   * @var Collection<Annotation>
   *
   * @ORM\OneToMany(targetEntity="Annotation", mappedBy="user")
   */
  private Collection $annotations;

  public function __construct()
  {
    $this->registeredOn  = new DateTime();
    $this->userGroups    = new ArrayCollection();
    $this->annotations   = new ArrayCollection();
  }

  /** String representation to be used in selection list. */
  public function selectionName(): string
  {
    if ($this->isOidc()) {
      return $this->getDisplayName();
    }

    return $this->getDisplayName() . ' (Local account)';
  }

  /**
   * Create a user from a token object.
   *
   * @throws OidcException
   */
  public static function createFromOidcUserData(OidcUserData $userData): User
  {
    $username = $userData->getEmail();

    // Username must not be empty!
    if (empty($username)) {
      throw new OidcException('Retrieved username from OIDC is empty!');
    }

    return (new User())
      ->setUsername($username)
      ->setIsOidc(true)
      ->update($userData);
  }

  /** Custom sorter, based on display name. */
  public static function sortOnDisplayName(User $a, User $b): int
  {
    return strcmp($a->getDisplayName(), $b->getDisplayName());
  }

  /**
   * Update the user with the latest information from the token
   * Do not update the username, this should be the same anyways.
   */
  public function update(OidcUserData $userData): User
  {
    return $this
      ->setDisplayName($userData->getDisplayName())
      ->setFullName($userData->getFullName())
      ->setGivenName($userData->getGivenName())
      ->setFamilyName($userData->getFamilyName());
  }

  /**
   * Get the mailer Address for this User.
   *
   * @return Address
   */
  public function getAddress()
  {
    return new Address($this->username, $this->getFullName());
  }

  /**
   * String representation of object.
   *
   * @see  http://php.net/manual/en/serializable.serialize.php
   *
   * @return string the string representation of the object or null
   *
   * @since 5.1.0
   */
  #[Override]
  public function serialize()
  {
    return serialize($this->__serialize());
  }

  public function __serialize(): array
  {
    return [
      $this->id,
      $this->username,
      $this->password,
      true, // BC-compatibility with serialized sessions
      $this->isOidc,
    ];
  }

  /**
   * Constructs the object.
   *
   * @see  http://php.net/manual/en/serializable.unserialize.php
   *
   * @param string $serialized the string representation of the object
   *
   * @return void
   *
   * @since 5.1.0
   */
  #[Override]
  public function unserialize($serialized)
  {
    $this->__unserialize(unserialize($serialized));
  }

  public function __unserialize(array $data): void
  {
    [
      $this->id,
      $this->username,
      $this->password,
      , //  BC-compatibility with serialized sessions
      $this->isOidc,
    ] = $data;
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
  #[Override]
  public function getRoles(): array
  {
    $roles = ['ROLE_USER'];

    if ($this->isAdmin()) {
      $roles[] = 'ROLE_SUPER_ADMIN';
    }

    return array_merge($roles, $this->securityRoles);
  }

  /**
   * Removes sensitive data from the user.
   *
   * This is important if, at any given point, sensitive information like
   * the plain-text password is stored on this object.
   */
  #[Override]
  public function eraseCredentials()
  {
  }

  #[Override]
  public function getUserIdentifier(): string
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

  public function isOidc(): bool
  {
    return $this->isOidc;
  }

  public function setIsOidc(bool $isOidc): User
  {
    $this->isOidc = $isOidc;

    return $this;
  }

  #[Override]
  public function getPassword(): string
  {
    return $this->password ?? '';
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
   * Get registeredOn.
   *
   * @return DateTime
   */
  public function getRegisteredOn()
  {
    return $this->registeredOn;
  }

  public function getLastUsed(): ?DateTime
  {
    return $this->lastUsed;
  }

  public function setLastUsed(DateTime $lastUsed): User
  {
    $this->lastUsed = $lastUsed;

    return $this;
  }

  /**
   * Set securityRoles.
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
   * Get securityRoles.
   *
   * @return array
   */
  public function getSecurityRoles()
  {
    return $this->securityRoles;
  }

  public function getGivenName(): string
  {
    return $this->givenName;
  }

  public function setGivenName(string $givenName): User
  {
    $this->givenName = $givenName;

    return $this;
  }

  public function getFamilyName(): string
  {
    return $this->familyName;
  }

  public function setFamilyName(string $familyName): User
  {
    $this->familyName = $familyName;

    return $this;
  }

  public function getFullName(): string
  {
    return $this->fullName;
  }

  public function setFullName(string $fullName): User
  {
    $this->fullName = $fullName;

    return $this;
  }

  public function getDisplayName(): string
  {
    return $this->displayName;
  }

  public function setDisplayName(string $displayName): User
  {
    $this->displayName = $displayName;

    return $this;
  }

  public function isAdmin(): bool
  {
    return $this->isAdmin;
  }

  public function setIsAdmin(bool $isAdmin): User
  {
    $this->isAdmin = $isAdmin;

    return $this;
  }

  public function getResetCode(): ?string
  {
    return $this->resetCode;
  }

  public function setResetCode(?string $resetCode): self
  {
    $this->resetCode = $resetCode;

    return $this;
  }

  public function getResetCodeValid(): ?DateTime
  {
    return $this->resetCodeValid;
  }

  public function setResetCodeValid(?DateTime $resetCodeValid): self
  {
    $this->resetCodeValid = $resetCodeValid;

    return $this;
  }

  /** @return Collection<UserGroup> */
  public function getUserGroups(): Collection
  {
    return $this->userGroups;
  }

  /** @return Collection<Annotation> */
  public function getAnnotations(): Collection
  {
    return $this->annotations;
  }
}
