<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Entity\Contracts\ReviewableInterface;
use App\Review\ReviewService;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use RuntimeException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class PendingChange
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\PendingChangeRepository")
 */
class PendingChange
{
  /**
   * Change types
   */
  public const CHANGE_TYPE_ADD = 'add';
  public const CHANGE_TYPE_EDIT = 'edit';
  public const CHANGE_TYPE_REMOVE = 'remove';
  public const CHANGE_TYPES = [
      self::CHANGE_TYPE_ADD,
      self::CHANGE_TYPE_EDIT,
      self::CHANGE_TYPE_REMOVE,
  ];

  use IdTrait;
  use Blameable;

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
   * The change type of the pending change
   *
   * @var string|null
   * @ORM\Column(type="string", length=10)
   *
   * @Assert\NotNull()
   * @Assert\Choice(choices=PendingChange::CHANGE_TYPES)
   */
  private $changeType;

  /**
   * The object type of the pending change
   *
   * @var string|null
   * @ORM\Column(type="string", length=255)
   *
   * @Assert\NotBlank(allowNull=false)
   */
  private $objectType;

  /**
   * The object id of the pending change
   *
   * @var int|null
   *
   * @ORM\Column(type="integer", nullable=true)
   */
  private $objectId;

  /**
   * JSON encoded object
   *
   * @var string|null
   * @ORM\Column(type="text")
   *
   * @Assert\NotBlank(allowNull=false)
   */
  private $payload;

  /**
   * Changed fields in the object
   *
   * @var array|null
   *
   * @ORM\Column(type="json")
   *
   * @Assert\NotNull()
   */
  private $changedFields;

  /**
   * The owner of the pending change (aka, the user who created it)
   *
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   *
   * @Assert\NotNull()
   */
  private $owner;

  /**
   * When set, the review has been requested
   *
   * @var DateTime|null
   *
   * @ORM\Column(type="datetime", nullable=true)
   *
   * @Assert\Type("datetime")
   */
  private $requestedReviewAt;

  /**
   * The requested reviewer (if any)
   *
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=true)
   */
  private $requestedReviewBy;

  /**
   * If any, review comments on particular changes (per field) are stores here
   *
   * @var array|null
   *
   * @ORM\Column(type="json", nullable=true)
   *
   * @Assert\Type("array")
   */
  private $reviewComments;

  /**
   * @return StudyArea
   */
  public function getStudyArea(): StudyArea
  {
    return $this->studyArea;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return PendingChange
   */
  public function setStudyArea(StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getChangeType(): ?string
  {
    return $this->changeType;
  }

  /**
   * @param string|null $changeType
   *
   * @return PendingChange
   */
  public function setChangeType(?string $changeType): self
  {
    $this->changeType = $changeType;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getObjectType(): ?string
  {
    return $this->objectType;
  }

  /**
   * @param string|null $objectType
   *
   * @return PendingChange
   */
  public function setObjectType(?string $objectType): self
  {
    $this->objectType = $objectType;

    return $this;
  }

  /**
   * @return int|null
   */
  public function getObjectId(): ?int
  {
    return $this->objectId;
  }

  /**
   * @param int|null $objectId
   *
   * @return PendingChange
   */
  public function setObjectId(?int $objectId): self
  {
    $this->objectId = $objectId;

    return $this;
  }

  /**
   * Validates the object id field, which must be empty for new objects, but filled for existing objects
   *
   * @Assert\Callback()
   *
   * @param ExecutionContextInterface $context
   * @param                           $payload
   */
  public function validateObjectId(ExecutionContextInterface $context, $payload)
  {
    $violation = NULL;
    if ($this->changeType == self::CHANGE_TYPE_ADD) {
      if ($this->objectId !== NULL) {
        $violation = $context->buildViolation('Object ID cannot be set!');
      }
    } else {
      if ($this->objectId === NULL) {
        $violation = $context->buildViolation('Object ID must be set!');
      }
    }

    if ($violation) {
      $violation->atPath('objectId')
          ->addViolation();
    }
  }

  /**
   * Set the object version that must be stored. Will be serialized
   *
   * @param ReviewableInterface $object
   *
   * @return $this
   */
  public function setObject(ReviewableInterface $object): self
  {
    $this->payload = $object
        ? ReviewService::getDataSnapshot($object)
        : NULL;

    return $this;
  }

  /**
   * Retrieve the change object, which is deserialized in the stored type
   *
   * @return ReviewableInterface|null
   */
  public function getObject(): ?ReviewableInterface
  {
    if (!$this->objectType) {
      throw new RuntimeException("Object type is not set, so data object cannot be retrieved!");
    }

    if (!$this->payload) {
      return NULL;
    }

    return ReviewService::getObjectFromSnapshot($this->payload, $this->objectType);
  }

  /**
   * @return array|null
   */
  public function getChangedFields(): ?array
  {
    return $this->changedFields;
  }

  /**
   * @param array|null $changedFields
   *
   * @return PendingChange
   */
  public function setChangedFields(?array $changedFields): self
  {
    $this->changedFields = $changedFields;

    return $this;
  }

  /**
   * @return User|null
   */
  public function getOwner(): ?User
  {
    return $this->owner;
  }

  /**
   * @param User|null $owner
   *
   * @return PendingChange
   */
  public function setOwner(?User $owner): self
  {
    $this->owner = $owner;

    return $this;
  }

  /**
   * @return DateTime|null
   */
  public function getRequestedReviewAt(): ?DateTime
  {
    return $this->requestedReviewAt;
  }

  /**
   * @param DateTime|null $requestedReviewAt
   *
   * @return PendingChange
   */
  public function setRequestedReviewAt(?DateTime $requestedReviewAt): self
  {
    $this->requestedReviewAt = $requestedReviewAt;

    return $this;
  }

  /**
   * @return User|null
   */
  public function getRequestedReviewBy(): ?User
  {
    return $this->requestedReviewBy;
  }

  /**
   * @param User|null $requestedReviewBy
   *
   * @return PendingChange
   */
  public function setRequestedReviewBy(?User $requestedReviewBy): self
  {
    $this->requestedReviewBy = $requestedReviewBy;

    return $this;
  }

  /**
   * @return array|null
   */
  public function getReviewComments(): ?array
  {
    return $this->reviewComments;
  }

  /**
   * @param array|null $reviewComments
   *
   * @return PendingChange
   */
  public function setReviewComments(?array $reviewComments): self
  {
    $this->reviewComments = $reviewComments;

    return $this;
  }

}
