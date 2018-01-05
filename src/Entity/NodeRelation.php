<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
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
   * @ORM\JoinColumn(name="left_node_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $leftNode;

  /**
   * @var Node
   *
   * @ORM\ManyToOne(targetEntity="Node", inversedBy="indirectRelations")
   * @ORM\JoinColumn(name="right_node_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $rightNode;

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
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @return Node|null
   */
  public function getLeftNode(): ?Node
  {
    return $this->leftNode;
  }

  /**
   * @param Node $leftNode
   *
   * @return NodeRelation
   */
  public function setLeftNode(Node $leftNode): NodeRelation
  {
    $this->leftNode = $leftNode;

    return $this;
  }

  /**
   * @return Node|null
   */
  public function getRightNode(): ?Node
  {
    return $this->rightNode;
  }

  /**
   * @param Node $rightNode
   *
   * @return NodeRelation
   */
  public function setRightNode(Node $rightNode): NodeRelation
  {
    $this->rightNode = $rightNode;

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
