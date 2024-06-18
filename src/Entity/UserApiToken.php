<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Repository\UserApiTokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Helper\StringHelper;
use Drenso\Shared\Interfaces\IdInterface;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserApiTokenRepository::class)]
#[ORM\Table]
#[ORM\Index(columns: ['token_id'])]
#[JMSA\ExclusionPolicy('all')]
class UserApiToken implements UserInterface, PasswordAuthenticatedUserInterface, IdInterface
{
  use IdTrait;
  use Blameable;

  /** The user linked with this token. */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(referencedColumnName: 'id', nullable: false)]
  private User $user; // Default in constructor

  /** The token id. */
  #[ORM\Column(length: 255, unique: true)]
  private string $tokenId; // Default in constructor

  /** The encoded token. */
  #[ORM\Column(length: 255)]
  private string $token; // Default in constructor

  #[Assert\Length(max: 255)]
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $description = null;

  #[ORM\Column(nullable: true)]
  private ?DateTimeImmutable $validUntil = null;

  #[ORM\Column(nullable: true)]
  private ?DateTimeImmutable $lastUsed = null;

  public function __construct(User $user, string $token)
  {
    $this->user    = $user;
    $this->tokenId = bin2hex(random_bytes(10));
    $this->token   = $token;
  }

  public function getUser(): User
  {
    return $this->user;
  }

  public function getTokenId(): string
  {
    return $this->tokenId;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = StringHelper::emptyToNull($description);

    return $this;
  }

  public function getValidUntil(): ?DateTimeImmutable
  {
    return $this->validUntil;
  }

  public function setValidUntil(?DateTimeImmutable $validUntil): self
  {
    $this->validUntil = $validUntil;

    return $this;
  }

  public function getLastUsed(): ?DateTimeImmutable
  {
    return $this->lastUsed;
  }

  public function setLastUsed(?DateTimeImmutable $lastUsed): self
  {
    $this->lastUsed = $lastUsed;

    return $this;
  }

  #[Override]
  public function getPassword(): ?string
  {
    return $this->token;
  }

  /** Used to upgrade the token to the newest encoding */
  public function setPassword(string $password): self
  {
    $this->token = $password;

    return $this;
  }

  #[Override]
  public function getRoles(): array
  {
    return $this->getUser()->getRoles();
  }

  #[Override]
  public function getUserIdentifier(): string
  {
    return $this->getUser()->getUserIdentifier();
  }

  #[Override]
  public function eraseCredentials()
  {
    // Nothing to do
  }
}
