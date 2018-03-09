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
   * ConceptRelation constructor.
   */
  public function __construct()
  {
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
}
