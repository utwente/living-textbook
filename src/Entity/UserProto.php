<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Repository\UserProtoRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Override;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * These are users who have been invited to create a local account, but have not responded yet
 * Implements the user interface for easy password hashing and checking.
 */
#[UniqueEntity(['email'], errorPath: 'email')]
#[ORM\Entity(repositoryClass: UserProtoRepository::class)]
#[ORM\Table]
#[ORM\Index(columns: ['email'])]
class UserProto implements UserInterface, PasswordAuthenticatedUserInterface, IdInterface
{
  use IdTrait;
  use Blameable;

  /** The email address that has been invited. */
  #[Assert\NotBlank]
  #[Assert\Email]
  #[ORM\Column]
  private string $email = '';

  /** The invited at timestamp. */
  #[Assert\NotNull]
  #[ORM\Column]
  private DateTime $invitedAt;

  /** Hashed temporary password. */
  #[Assert\NotBlank]
  #[ORM\Column(unique: true)]
  private string $password = '';

  public function __construct()
  {
    $this->invitedAt = new DateTime();
  }

  #[Override]
  public function getRoles(): array
  {
    return [];
  }

  #[Override]
  public function getUserIdentifier(): string
  {
    return $this->email;
  }

  #[Override]
  public function eraseCredentials()
  {
    // Nothing to do
  }

  public function getEmail(): string
  {
    return $this->email;
  }

  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  public function getInvitedAt(): DateTime
  {
    return $this->invitedAt;
  }

  public function setInvitedAt(DateTime $invitedAt): self
  {
    $this->invitedAt = $invitedAt;

    return $this;
  }

  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  #[Override]
  public function getPassword(): string
  {
    return $this->password;
  }
}
