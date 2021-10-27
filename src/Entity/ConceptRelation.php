<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ConceptRelation
 *
 * @ApiResource(
 *     attributes={},
 *     collectionOperations={"get"={}, "post"={}},
 *     itemOperations={"get"={}, "put"={}, "delete"={}},
 *     normalizationContext={"groups"={"conceptrelation:read", "concept:read"}},
 *     denormalizationContext={"groups"={"conceptrelation:write"}},
 * )
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ConceptRelationRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class ConceptRelation
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var Concept
   *
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="outgoingRelations")
   * @ORM\JoinColumn(name="source_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"review_change"})
   * @JMSA\Type(Concept::class)
   * @JMSA\MaxDepth(2)
   *
   * @Groups({"conceptrelation:write"})
   *
   */
  private $source;

  /**
   * @var Concept
   *
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="incomingRelations")
   * @ORM\JoinColumn(name="target_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"review_change"})
   * @JMSA\Type(Concept::class)
   * @JMSA\MaxDepth(2)
   *
   * * @Groups({"conceptrelation:write"})
   */
  private $target;


  /**
   * @var RelationType
   *
   * @ORM\ManyToOne(targetEntity="RelationType", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="relation_type", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"review_change"})
   * @JMSA\Type(RelationType::class)
   * @JMSA\MaxDepth(2)
   *
   * @Groups({"conceptrelation:write"})
   */
  private $relationType;

  /**
   * The position field will be filled automatically by a callback in the concept,
   * in order to force the desired positioning
   *
   * @var int
   *
   * @ORM\Column(name="outgoing_position", type="integer", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\GreaterThanOrEqual(value="0")
   */
  private $outgoingPosition = 0;

  /**
   * The position field will be filled automatically by a callback in the concept,
   * in order to force the desired positioning
   *
   * @var int
   *
   * @ORM\Column(name="incoming_position", type="integer", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\GreaterThanOrEqual(value="0")
   */
  private $incomingPosition = 0;

  /**
   * @var mixed
   *
   * @ORM\Column(name="modelCfg", type="json_document", nullable=true, options={"jsonb": true})
   * @JMSA\Type("mixed")
   *
   * @Groups({"concept:read", "conceptrelation:read", "conceptrelation:write"})
   */
  private $modelCfg;

  /**
   * @return int|null
   *
   * @JMSA\VirtualProperty()
   * @JMSA\SerializedName("target")
   * @JMSA\Expose()
   *
   * @Groups({"concept:read", "conceptrelation:read"})
   *
   */
  public function getTargetId(): ?int
  {
    return $this->getTarget() ? $this->getTarget()->getId() : NULL;
  }

  /**
   * @return int|null
   *
   * @JMSA\VirtualProperty()
   * @JMSA\SerializedName("source")
   * @JMSA\Expose()
   *
   * @Groups({"concept:read", "conceptrelation:read"})
   */
  public function getSourceId(): ?int
  {
    return $this->getSource() ? $this->getSource()->getId() : NULL;
  }

  /**
   * @return string
   *
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   *
   * @Groups({"concept:read", "conceptrelation:read"})
   *
   */
  public function getRelationName(): string
  {
    return $this->relationType ? $this->relationType->getName() : '';
  }

  /**
   * @return string|null
   *
   * @JMSA\VirtualProperty()
   * @JMSA\Expose()
   *
   * @Groups({"concept:read", "conceptrelation:read"})
   *
   */
  public function getRelationDescription(): ?string
  {
    return $this->relationType ? $this->relationType->getDescription() : '';
  }

  /**
   * @return Concept|null
   */
  public function getSource(): ?Concept
  {
    return $this->source;
  }

  /**
   * @param Concept $source
   *
   * @return ConceptRelation
   */
  public function setSource(Concept $source): ConceptRelation
  {
    $this->source = $source;

    return $this;
  }

  /**
   * @return Concept|null
   */
  public function getTarget(): ?Concept
  {
    return $this->target;
  }

  /**
   * @param Concept $target
   *
   * @return ConceptRelation
   */
  public function setTarget(Concept $target): ConceptRelation
  {
    $this->target = $target;

    return $this;
  }

  /**
   * @return RelationType|null
   */
  public function getRelationType(): ?RelationType
  {
    return $this->relationType;
  }

  /**
   * @param RelationType|null $relationType
   *
   * @return ConceptRelation
   */
  public function setRelationType(?RelationType $relationType): ConceptRelation
  {
    // Return on null, as the type might be deleted
    if ($relationType === NULL) return $this;

    $this->relationType = $relationType;

    return $this;
  }

  /**
   * @return int
   */
  public function getOutgoingPosition(): int
  {
    return $this->outgoingPosition;
  }

  /**
   * @param int $outgoingPosition
   *
   * @return ConceptRelation
   */
  public function setOutgoingPosition(int $outgoingPosition): ConceptRelation
  {
    $this->outgoingPosition = $outgoingPosition;

    return $this;
  }

  /**
   * @return int
   */
  public function getIncomingPosition(): int
  {
    return $this->incomingPosition;
  }

  /**
   * @param int $incomingPosition
   *
   * @return ConceptRelation
   */
  public function setIncomingPosition(int $incomingPosition): ConceptRelation
  {
    $this->incomingPosition = $incomingPosition;

    return $this;
  }

  /**
   * @return int|null
   *
   * @Groups({"concept:read", "conceptrelation:read"})
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * @return mixed|null
   */
  public function getModelCfg()
  {
    return $this->modelCfg;
  }

  /**
   * @param mixed $modelCfg
   *
   * @return ConceptRelation
   */
  public function setModelCfg($modelCfg): ConceptRelation
  {
    $this->modelCfg = $modelCfg;

    return $this;
  }
}
