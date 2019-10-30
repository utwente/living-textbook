<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Entity\Contracts\ReviewableInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
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

  // Serializer details
  /** @var SerializerInterface|null */
  private static $serializer = NULL;
  /** @var SerializationContext|null */
  private static $serializationContext = NULL;
  private const SERIALIZER_FORMAT = 'json';

  private static function getSerializer(): SerializerInterface
  {
    if (!self::$serializer) {
      $serializerBuilder = SerializerBuilder::create();
      self::$serializer  = $serializerBuilder->build();
    }

    return self::$serializer;
  }

  private static function getSerializationContext(): SerializationContext
  {
    if (!self::$serializationContext) {
      $context = SerializationContext::create();
      $context->setGroups(['review_change']);
      self::$serializationContext = $context;
    }

    return self::$serializationContext;
  }

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
   * @param $object
   *
   * @return $this
   */
  public function setObject($object): self
  {
    $this->payload = $object
        ? self::getSerializer()->serialize($object, self::SERIALIZER_FORMAT, self::getSerializationContext())
        : NULL;

    return $this;
  }

  /**
   * Retrieve the change object, which is deserialized in the stored type
   *
   * @return ReviewableInterface|null
   */
  public function getObject()
  {
    if (!$this->objectType) {
      throw new RuntimeException("Object type is not set, so data object cannot be retrieved!");
    }

    if (!$this->payload) {
      return NULL;
    }

    $object = self::getSerializer()->deserialize($this->payload, $this->objectType, self::SERIALIZER_FORMAT);
    assert($object instanceof ReviewableInterface);

    return $object;
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

}
