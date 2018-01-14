<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
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
 * @JMS\ExclusionPolicy("all")
 */
class ConceptRelation
{

  use Blameable;
  use SoftDeletable;

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var Concept
   *
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="relations")
   * @ORM\JoinColumn(name="source_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $source;

  /**
   * @var Concept
   *
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="indirectRelations")
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
   * @return int
   *
   * @JMS\VirtualProperty("target")
   * @JMS\Expose()
   */
  public function getTargetId(): int
  {
    return $this->getTarget() ? $this->getTarget()->getId() : NULL;
  }

  /**
   * @return string
   *
   * @JMS\VirtualProperty("relationName")
   * @JMS\Expose()
   */
  public function getRelationName(): string
  {
    return $this->relationType ? $this->relationType->getName() : '';
  }

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
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
