<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Node
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\NodeRelationRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMS\ExclusionPolicy("all")
 */
class NodeRelation
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
   * @var Node
   *
   * @ORM\ManyToOne(targetEntity="Node", inversedBy="relations")
   * @ORM\JoinColumn(name="source_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $source;

  /**
   * @var Node
   *
   * @ORM\ManyToOne(targetEntity="Node", inversedBy="indirectRelations")
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
   * NodeRelation constructor.
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
   * @return Node|null
   */
  public function getSource(): ?Node
  {
    return $this->source;
  }

  /**
   * @param Node $source
   *
   * @return NodeRelation
   */
  public function setSource(Node $source): NodeRelation
  {
    $this->source = $source;

    return $this;
  }

  /**
   * @return Node|null
   */
  public function getTarget(): ?Node
  {
    return $this->target;
  }

  /**
   * @param Node $target
   *
   * @return NodeRelation
   */
  public function setTarget(Node $target): NodeRelation
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
   * @return NodeRelation
   */
  public function setRelationType(RelationType $relationType): NodeRelation
  {
    $this->relationType = $relationType;

    return $this;
  }

}
