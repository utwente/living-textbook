<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Entity\Contracts\ReviewableInterface;
use App\Review\Exception\IncompatibleChangeMergeException;
use App\Review\Exception\OverlappingFieldsChangedException;
use App\Review\ReviewService;
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
   * Number are added to force time dependant ordering
   */
  public const CHANGE_TYPE_ADD = '10_add';
  public const CHANGE_TYPE_EDIT = '20_edit';
  public const CHANGE_TYPE_REMOVE = '30_remove';
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
   * The review this pending change belongs to, if any
   *
   * @var Review|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\Review", inversedBy="pendingChanges")
   * @ORM\JoinColumn(nullable=true)
   */
  private $review;

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
   * Cached deserialized object
   *
   * @var ReviewableInterface|null
   */
  private $cachedObject = NULL;

  /**
   * Duplicated the pending change, while setting the new marked fields as supplied
   *
   * @param array $changedFields
   *
   * @return PendingChange
   */
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
   * @param PendingChange $merge
   *
   * @return PendingChange
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
    $origData  = json_decode($this->payload, true);
    $mergeData = json_decode($merge->payload, true);
    foreach ($merge->getChangedFields() as $changedField) {
      $origData[$changedField] = $mergeData[$changedField];
      $this->changedFields[]   = $changedField;
    }

    // Order the changed fields
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
            'instance'          => 195,
            'definition'        => 190,
            'synonyms'          => 180,
            'introduction'      => 170,
            'priorKnowledge'    => 160,
            'learningOutcomes'  => 150,
            'theoryExplanation' => 140,
            'howTo'             => 130,
            'examples'          => 120,
            'externalResources' => 110,
            'selfAssessment'    => 100,
            'relations'         => 90,
            'incomingRelations' => 80,
            'contributors'      => 70,
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

    // Set updated data
    $this->payload = json_encode($origData);

    return $this;
  }

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

  public function getShortObjectType(): ?string
  {
    $pos = strrpos($this->objectType, '\\');
    if (!$pos || $pos >= strlen($this->objectType) - 1) {
      return $this->objectType;
    }

    return substr($this->objectType, $pos + 1);
  }

  /**
   * @param string|null $objectType
   *
   * @return PendingChange
   */
  public function setObjectType(?string $objectType): self
  {
    $this->cachedObject = NULL;
    $this->objectType   = $objectType;

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
    $this->cachedObject = NULL;
    $this->payload      = $object
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

    if ($this->cachedObject === NULL) {
      $this->cachedObject = ReviewService::getObjectFromSnapshot($this->payload, $this->objectType);
    }

    return $this->cachedObject;
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
   * @return Review|null
   */
  public function getReview(): ?Review
  {
    return $this->review;
  }

  /**
   * @param Review|null $review
   *
   * @return PendingChange
   */
  public function setReview(?Review $review): self
  {
    $this->review = $review;

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
   * @return self
   */
  public function setReviewComments(?array $reviewComments): self
  {
    $this->reviewComments = $reviewComments;

    return $this;
  }

}
