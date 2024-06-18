<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Repository\ConceptRelationRepository;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMSA\ExclusionPolicy("all")
 */
#[ORM\Entity(repositoryClass: ConceptRelationRepository::class)]
#[ORM\Table]
class ConceptRelation implements IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(Concept::class)
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'outgoingRelations')]
  #[ORM\JoinColumn(name: 'source_id', referencedColumnName: 'id', nullable: false)]
  private ?Concept $source = null;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(Concept::class)
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'incomingRelations')]
  #[ORM\JoinColumn(name: 'target_id', referencedColumnName: 'id', nullable: false)]
  private ?Concept $target = null;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(RelationType::class)
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(name: 'relation_type', referencedColumnName: 'id', nullable: false)]
  private ?RelationType $relationType = null;

  /**
   * The position field will be filled automatically by a callback in the concept,
   * in order to force the desired positioning.
   */
  #[Assert\NotNull]
  #[Assert\GreaterThanOrEqual(value: '0')]
  #[ORM\Column(name: 'outgoing_position', nullable: false)]
  private int $outgoingPosition = 0;

  /**
   * The position field will be filled automatically by a callback in the concept,
   * in order to force the desired positioning.
   */
  #[Assert\NotNull]
  #[Assert\GreaterThanOrEqual(value: '0')]
  #[ORM\Column(name: 'incoming_position', nullable: false)]
  private int $incomingPosition = 0;

  #[ORM\Column(nullable: true)]
  private ?array $dotronConfig = null;

  /**
   * @JMSA\VirtualProperty()
   *
   * @JMSA\SerializedName("target")
   *
   * @JMSA\Expose()
   */
  public function getTargetId(): ?int
  {
    return $this->getTarget()?->getId();
  }

  public function getSourceId(): ?int
  {
    return $this->getSource()?->getId();
  }

  /**
   * @JMSA\VirtualProperty()
   *
   * @JMSA\Expose()
   */
  public function getRelationName(): string
  {
    return $this->relationType ? $this->relationType->getName() : '';
  }

  public function getSource(): ?Concept
  {
    return $this->source;
  }

  public function setSource(Concept $source): ConceptRelation
  {
    $this->source = $source;

    return $this;
  }

  public function getTarget(): ?Concept
  {
    return $this->target;
  }

  public function setTarget(Concept $target): ConceptRelation
  {
    $this->target = $target;

    return $this;
  }

  public function getRelationType(): ?RelationType
  {
    return $this->relationType;
  }

  public function setRelationType(?RelationType $relationType): ConceptRelation
  {
    // Return on null, as the type might be deleted
    if ($relationType === null) {
      return $this;
    }

    $this->relationType = $relationType;

    return $this;
  }

  public function getOutgoingPosition(): int
  {
    return $this->outgoingPosition;
  }

  public function setOutgoingPosition(int $outgoingPosition): ConceptRelation
  {
    $this->outgoingPosition = $outgoingPosition;

    return $this;
  }

  public function getIncomingPosition(): int
  {
    return $this->incomingPosition;
  }

  public function setIncomingPosition(int $incomingPosition): ConceptRelation
  {
    $this->incomingPosition = $incomingPosition;

    return $this;
  }

  public function getDotronConfig(): ?array
  {
    return $this->dotronConfig;
  }

  public function setDotronConfig(?array $dotronConfig): self
  {
    $this->dotronConfig = $dotronConfig;

    return $this;
  }
}
