<?php

namespace App\Entity;

use App\Database\Traits\IdTrait;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Repository\PageLoadRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/** TODO Migrate array properties to JSON. */
#[ORM\Entity(repositoryClass: PageLoadRepository::class)]
#[ORM\Table]
class PageLoad implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;

  #[Assert\NotNull]
  #[Assert\NotBlank]
  #[ORM\Column(name: 'user_id', length: 255)]
  private ?string $userId = null;

  #[Assert\NotNull]
  #[ORM\Column(name: 'timestamp')]
  private ?DateTime $timestamp = null;

  #[Assert\NotNull]
  #[Assert\NotBlank]
  #[ORM\Column(name: 'session_id', type: Types::GUID)]
  private ?string $sessionId = null;

  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  #[Assert\NotNull]
  #[Assert\NotBlank]
  #[Assert\Length(max: 1024)]
  #[ORM\Column(name: 'path', length: 1024)]
  private ?string $path = null;

  #[Assert\Type('array')]
  #[ORM\Column(name: 'path_context', type: Types::JSON, nullable: true)]
  private ?array $pathContext = null;

  #[Assert\Length(max: 1024)]
  #[ORM\Column(name: 'origin', length: 1024, nullable: true)]
  private ?string $origin = null;

  #[Assert\Type('array')]
  #[ORM\Column(name: 'origin_context', type: Types::JSON, nullable: true)]
  private ?array $originContext = null;

  public function getUserId(): string
  {
    return $this->userId;
  }

  public function setUserId(string $userId): self
  {
    $this->userId = $userId;

    return $this;
  }

  public function getTimestamp(): DateTime
  {
    return $this->timestamp;
  }

  public function setTimestamp(DateTime $timestamp): self
  {
    $this->timestamp = $timestamp;

    return $this;
  }

  public function getSessionId(): string
  {
    return $this->sessionId;
  }

  public function setSessionId(string $sessionId): self
  {
    $this->sessionId = $sessionId;

    return $this;
  }

  #[Override]
  public function getStudyArea(): StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getPath(): string
  {
    return $this->path;
  }

  public function setPath(string $path): self
  {
    $this->path = $path;

    return $this;
  }

  public function getPathContext(): ?array
  {
    return $this->pathContext;
  }

  public function setPathContext(?array $pathContext): self
  {
    $this->pathContext = $pathContext;

    return $this;
  }

  public function getOrigin(): ?string
  {
    return $this->origin;
  }

  public function setOrigin(?string $origin): self
  {
    $this->origin = $origin;

    return $this;
  }

  public function getOriginContext(): ?array
  {
    return $this->originContext;
  }

  public function setOriginContext(?array $originContext): self
  {
    $this->originContext = $originContext;

    return $this;
  }
}
