<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ConceptRelation
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
   */
  private $source;

  /**
   * @var Concept
   *
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="incomingRelations")
   * @ORM\JoinColumn(name="target_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $target;

  /**
   * @var RelationType
   *
   * @ORM\ManyToOne(targetEntity="RelationType")
   * @ORM\JoinColumn(name="relation_type", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
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
  private $outgoingPosition;

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
  private $incomingPosition;

  /**
   * ConceptRelation constructor.
   */
  public function __construct()
  {
    $this->incomingPosition = 0;
    $this->outgoingPosition = 0;
  }

  /**
   * @return int|null
   *
   * @JMSA\VirtualProperty("target")
   * @JMSA\Expose()
   */
  public function getTargetId(): ?int
  {
    return $this->getTarget() ? $this->getTarget()->getId() : NULL;
  }

  /**
   * @return int|null
   */
  public function getSourceId(): ?int
  {
    return $this->getSource() ? $this->getSource()->getId() : NULL;
  }

  /**
   * @return string
   *
   * @JMSA\VirtualProperty("relationName")
   * @JMSA\Expose()
   */
  public function getRelationName(): string
  {
    return $this->relationType ? $this->relationType->getName() : '';
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
   * @param RelationType $relationType
   *
   * @return ConceptRelation
   */
  public function setRelationType(RelationType $relationType): ConceptRelation
  {
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

}
