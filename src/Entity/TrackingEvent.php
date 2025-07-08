<?php

namespace App\Entity;

use App\Database\Traits\IdTrait;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Repository\TrackingEventRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * TODO Migrate array property to JSON.
 */
#[ORM\Entity(repositoryClass: TrackingEventRepository::class)]
#[ORM\Table]
class TrackingEvent implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;

  /** The supported events */
  final public const array SUPPORTED_EVENTS = [
    'concept_browser_open',
    'concept_browser_open_concept',
    'concept_browser_close',
    'learning_path_browser_open',
    'learning_path_browser_open_concept',
    'learning_path_browser_close',
    'general_link_click',
  ];

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
  #[Assert\Choice(choices: self::SUPPORTED_EVENTS)]
  #[Assert\Length(max: 50)]
  #[ORM\Column(name: 'event', length: 50)]
  private ?string $event = null;

  #[Assert\Type('array')]
  #[ORM\Column(name: 'context', type: Types::ARRAY, nullable: true)]
  private ?array $context = null;

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

  public function getEvent(): string
  {
    return $this->event;
  }

  public function setEvent(string $event): self
  {
    $this->event = $event;

    return $this;
  }

  public function getContext(): ?array
  {
    return $this->context;
  }

  public function setContext(?array $context): self
  {
    $this->context = $context;

    return $this;
  }
}
