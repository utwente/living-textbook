<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Entity\Contracts\ReviewableInterface;
use App\Repository\PendingChangeRepository;
use App\Review\Exception\IncompatibleChangeMergeException;
use App\Review\Exception\OverlappingFieldsChangedException;
use App\Review\ReviewService;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use RuntimeException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: PendingChangeRepository::class)]
#[ORM\Table]
class PendingChange implements IdInterface
{
  use IdTrait;
  use Blameable;

  /**
   * Change types
   * Number are added to force time dependant ordering.
   */
  final public const string CHANGE_TYPE_ADD    = '10_add';
  final public const string CHANGE_TYPE_EDIT   = '20_edit';
  final public const string CHANGE_TYPE_REMOVE = '30_remove';
  final public const array CHANGE_TYPES        = [
    self::CHANGE_TYPE_ADD,
    self::CHANGE_TYPE_EDIT,
    self::CHANGE_TYPE_REMOVE,
  ];

  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  /** The change type of the pending change. */
  #[Assert\NotNull]
  #[Assert\Choice(choices: PendingChange::CHANGE_TYPES)]
  #[ORM\Column(length: 10)]
  private ?string $changeType = null;

  /** The object type of the pending change. */
  #[Assert\NotBlank(allowNull: false)]
  #[ORM\Column(length: 255)]
  private ?string $objectType = null;

  /** The object id of the pending change. */
  #[ORM\Column(nullable: true)]
  private ?int $objectId = null;

  /**
   * JSON encoded object.
   *
   * @var string|null
   */
  #[Assert\NotBlank(allowNull: false)]
  #[ORM\Column(type: Types::TEXT)]
  private $payload;

  /**
   * Changed fields in the object.
   *
   * @var array|null
   */
  #[Assert\NotNull]
  #[ORM\Column(type: Types::JSON)]
  private $changedFields;

  /** The owner of the pending change (aka, the user who created it). */
  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $owner = null;

  /** The review this pending change belongs to, if any. */
  #[ORM\ManyToOne(inversedBy: 'pendingChanges')]
  #[ORM\JoinColumn(nullable: true)]
  private ?Review $review = null;

  /** If any, review comments on particular changes (per field) are stores here. */
  #[ORM\Column(nullable: true)]
  private ?array $reviewComments = null;

  /** Cached deserialized object. */
  private ?ReviewableInterface $cachedObject = null;

  /** Duplicated the pending change, while setting the new marked fields as supplied. */
  public function duplicate(array $changedFields): PendingChange
  {
    $new = (new PendingChange())
      ->setStudyArea($this->getStudyArea())
      ->setChangeType($this->getChangeType())
      ->setObjectType($this->getObjectType())
      ->setObjectId($this->getObjectId())
      ->setOwner($this->getOwner())
      ->setChangedFields($changedFields);

    $new->payload = $this->payload;

    return $new;
  }

  /**
   * Merge the supplied pending changes. The second will be merged into the first one.
   * This action should on merge the difference when the properties do not overlap!
   *
   * @throws IncompatibleChangeMergeException
   * @throws OverlappingFieldsChangedException
   */
  public function merge(PendingChange $merge): self
  {
    // Validate whether the pending change to be merged is of the same type
    if ($this->getObjectType() !== $merge->getObjectType()
        || $this->getObjectId() !== $merge->getObjectId()) {
      throw new IncompatibleChangeMergeException($this, $merge);
    }

    // Validate whether there are no overlapping fields
    if (0 !== count(array_intersect($this->getChangedFields(), $merge->getChangedFields()))) {
      throw new OverlappingFieldsChangedException($this, $merge);
    }

    // Merge the data, by updating the serialized content
    $origData  = json_decode($this->payload ?? '[]', true);
    $mergeData = json_decode($merge->payload ?? '[]', true);
    foreach ($merge->getChangedFields() as $changedField) {
      /* @phan-suppress-next-line PhanTypeArraySuspiciousNullable */
      $origData[$changedField] = $mergeData[$changedField];
      $this->changedFields[]   = $changedField;
    }

    // Order the changed fields
    $this->orderChangedFields();

    // Set updated data
    $this->payload = json_encode($origData);

    return $this;
  }

  /** Order the changes fields */
  public function orderChangedFields()
  {
    switch ($this->objectType) {
      case Abbreviation::class:
        $sortOrder = [
          'abbreviation' => 200,
          'meaning'      => 150,
        ];
        break;
      case Concept::class:
        $sortOrder = [
          'name'              => 200,
          'instance'          => 190,
          'definition'        => 180,
          'introduction'      => 170,
          'theoryExplanation' => 160,
          'examples'          => 150,
          'howTo'             => 145,
          'synonyms'          => 140,
          'externalResources' => 130,
          'learningOutcomes'  => 120,
          'priorKnowledge'    => 110,
          'selfAssessment'    => 90,
          'relations'         => 80,
          'incomingRelations' => 70,
          'contributors'      => 60,
        ];
        break;
      case Contributor::class:
        $sortOrder = [
          'name'        => 200,
          'description' => 150,
          'url'         => 100,
        ];
        break;
      case ExternalResource::class:
        $sortOrder = [
          'title'       => 200,
          'description' => 150,
          'url'         => 100,
        ];
        break;
      case LearningOutcome::class:
        $sortOrder = [
          'number' => 200,
          'name'   => 150,
          'text'   => 100,
        ];
        break;
      case LearningPath::class:
        $sortOrder = [
          'name'         => 200,
          'introduction' => 150,
          'question'     => 100,
          'elements'     => 50,
        ];
        break;
      case RelationType::class:
        $sortOrder = [
          'name'        => 200,
          'description' => 150,
        ];
        break;
      default:
        $sortOrder = [];
    }

    usort($this->changedFields, function (string $a, string $b) use ($sortOrder) {
      if (!array_key_exists($a, $sortOrder) || !array_key_exists($b, $sortOrder)) {
        return 0;
      }

      return $sortOrder[$b] <=> $sortOrder[$a];
    });
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

  public function getChangeType(): ?string
  {
    return $this->changeType;
  }

  public function setChangeType(?string $changeType): self
  {
    $this->changeType = $changeType;

    return $this;
  }

  public function getObjectType(): ?string
  {
    return $this->objectType;
  }

  public function getShortObjectType(): ?string
  {
    if ($this->objectType === null) {
      return null;
    }

    $pos = strrpos($this->objectType, '\\');
    if (!$pos || $pos >= strlen($this->objectType) - 1) {
      return $this->objectType;
    }

    return substr($this->objectType, $pos + 1);
  }

  public function setObjectType(?string $objectType): self
  {
    $this->cachedObject = null;
    $this->objectType   = $objectType;

    return $this;
  }

  public function getObjectId(): ?int
  {
    return $this->objectId;
  }

  public function setObjectId(?int $objectId): self
  {
    $this->objectId = $objectId;

    return $this;
  }

  /** Validates the object id field, which must be empty for new objects, but filled for existing objects. */
  #[Assert\Callback]
  public function validateObjectId(ExecutionContextInterface $context, $payload)
  {
    $violation = null;
    if ($this->changeType == self::CHANGE_TYPE_ADD) {
      if ($this->objectId !== null) {
        $violation = $context->buildViolation('Object ID cannot be set!');
      }
    } else {
      if ($this->objectId === null) {
        $violation = $context->buildViolation('Object ID must be set!');
      }
    }

    if ($violation) {
      $violation->atPath('objectId')
        ->addViolation();
    }
  }

  /**
   * Set the object version that must be stored. Will be serialized.
   *
   * @return $this
   */
  public function setObject(ReviewableInterface $object): self
  {
    $this->cachedObject = null;
    $this->payload      = $object
        ? ReviewService::getDataSnapshot($object)
        : null;

    return $this;
  }

  /** Retrieve the change object, which is deserialized in the stored type. */
  public function getObject(): ?ReviewableInterface
  {
    if (!$this->objectType) {
      throw new RuntimeException('Object type is not set, so data object cannot be retrieved!');
    }

    if (!$this->payload) {
      return null;
    }

    if ($this->cachedObject === null) {
      $this->cachedObject = ReviewService::getObjectFromSnapshot($this->payload, $this->objectType);
    }

    return $this->cachedObject;
  }

  public function getChangedFields(): ?array
  {
    return $this->changedFields;
  }

  public function setChangedFields(?array $changedFields): self
  {
    $this->changedFields = $changedFields;
    $this->orderChangedFields();

    return $this;
  }

  public function getOwner(): ?User
  {
    return $this->owner;
  }

  public function setOwner(?User $owner): self
  {
    $this->owner = $owner;

    return $this;
  }

  public function getReview(): ?Review
  {
    return $this->review;
  }

  public function setReview(?Review $review): self
  {
    $this->review = $review;

    return $this;
  }

  public function getReviewComments(): ?array
  {
    return $this->reviewComments;
  }

  public function setReviewComments(?array $reviewComments): self
  {
    $this->reviewComments = $reviewComments;

    return $this;
  }
}
