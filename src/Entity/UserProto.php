<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserProto
 * These are users who have been invited to create a local account, but have not responded yet
 * Implements the user interface for easy password hashing and checking.
 *
 * @ORM\Table(indexes={@ORM\Index(columns={"email"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserProtoRepository")
 *
 * @UniqueEntity({"email"}, errorPath="email")
 */
class UserProto implements UserInterface
{
  use IdTrait;
  use Blameable;

  /**
   * The email address that has been invited.
   *
   * @var string
   *
   * @ORM\Column(type="string")
   *
   * @Assert\NotBlank()
   * @Assert\Email()
   */
  private $email = '';

  /**
   * The invited at timestamp.
   *
   * @var DateTime
   *
   * @ORM\Column(type="datetime")
   *
   * @Assert\NotNull()
   */
  private $invitedAt;

  /**
   * Hashed temporary password.
   *
   * @var string;
   *
   * @ORM\Column(type="string", unique=true)
   *
   * @Assert\NotBlank()
   */
  private $password = '';

  public function __construct()
  {
    $this->invitedAt = new DateTime();
  }

  public function getRoles()
  {
    return [];
  }

  public function getSalt()
  {
    return null;
  }

  public function getUsername()
  {
    return $this->email;
  }

  public function eraseCredentials()
  {
    // Nothing to do
  }

  /** @return string */
  public function getEmail(): string
  {
    return $this->email;
  }

  /** @return UserProto */
  public function setEmail(string $email): self
  {
    $this->email = $email;

    return $this;
  }

  /** @return DateTime */
  public function getInvitedAt(): DateTime
  {
    return $this->invitedAt;
  }

  /** @return UserProto */
  public function setInvitedAt(DateTime $invitedAt): self
  {
    $this->invitedAt = $invitedAt;

    return $this;
  }

  /** @return UserProto */
  public function setPassword(string $password): self
  {
    $this->password = $password;

    return $this;
  }

  /** @return string */
  public function getPassword(): string
  {
    return $this->password;
  }
}
