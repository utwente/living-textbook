<?php

namespace App\Entity;

use App\Database\Traits\IdTrait;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TrackingEvent.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\TrackingEventRepository")
 */
class TrackingEvent implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;

  /** The supported events */
  final public const SUPPORTED_EVENTS = [
      'concept_browser_open',
      'concept_browser_open_concept',
      'concept_browser_close',
      'learning_path_browser_open',
      'learning_path_browser_open_concept',
      'learning_path_browser_close',
      'general_link_click',
  ];

  /**
   * @var string
   *
   * @ORM\Column(name="user_id", type="string", length=255)
   *
   * @Assert\NotNull()
   * @Assert\NotBlank()
   */
  private $userId;

  /**
   * @var DateTime
   *
   * @ORM\Column(name="timestamp", type="datetime")
   *
   * @Assert\NotNull()
   */
  private $timestamp;

  /**
   * @var string
   *
   * @ORM\Column(name="session_id", type="guid")
   *
   * @Assert\NotNull()
   * @Assert\NotBlank()
   */
  private $sessionId;

  /**
   * @var StudyArea
   *
   * @ORM\ManyToOne(targetEntity="StudyArea")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @var string
   *
   * @ORM\Column(name="event", type="string", length=50)
   *
   * @Assert\NotNull()
   * @Assert\Choice(choices=TrackingEvent::SUPPORTED_EVENTS)
   * @Assert\Length(max=50)
   */
  private $event;

  /**
   * @var array|null
   *
   * @ORM\Column(name="context", type="array", nullable=true)
   *
   * @Assert\Type("array")
   */
  private $context;

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
