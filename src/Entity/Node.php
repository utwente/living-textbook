<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
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
 * @ORM\Entity(repositoryClass="App\Repository\NodeRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMS\ExclusionPolicy("all")
 */
class Node
{

  use Blameable;
  use SoftDeletable;

  /**
   * @var int
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   *
   * @JMS\Expose()
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   * @Assert\Length(min=3, max=255)
   *
   * @JMS\Expose()
   */
  private $name;

  /**
   * @var ArrayCollection|NodeRelation[]
   *
   * @ORM\OneToMany(targetEntity="NodeRelation", mappedBy="leftNode", cascade={"persist","remove"})
   *
   * @Assert\NotNull()
   */
  private $relations;

  /**
   * @var ArrayCollection|NodeRelation[]
   *
   * @ORM\OneToMany(targetEntity="NodeRelation", mappedBy="rightNode", cascade={"persist","remove"})
   *
   * @Assert\NotNull()
   */
  private $indirectRelations;

  /**
   * Node constructor.
   */
  public function __construct()
  {
    $this->relations         = new ArrayCollection();
    $this->indirectRelations = new ArrayCollection();
  }

  /**
   * @return int
   *
   * @JMS\VirtualProperty("numberOfLinks")
   */
  public function getNumberOfLinks(): int
  {
    return count($this->relations) + count($this->indirectRelations);
  }

  /**
   * @return int
   */
  public function getId(): int
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   *
   * @return Node
   */
  public function setName(string $name): Node
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return ArrayCollection|Node[]
   */
  public function getRelations()
  {
    return $this->relations;
  }

  /**
   * @param NodeRelation $nodeRelation
   *
   * @return $this
   */
  public function addRelation(NodeRelation $nodeRelation): Node
  {
    // Check whether the left node is set
    if (!$nodeRelation->getLeftNode()) {
      $nodeRelation->setLeftNode($this);
    }

    $this->relations->add($nodeRelation);

    return $this;
  }

  /**
   * @param NodeRelation $nodeRelation
   *
   * @return $this
   */
  public function removeRelation(NodeRelation $nodeRelation): Node
  {
    $this->relations->removeElement($nodeRelation);

    return $this;
  }

  /**
   * @return ArrayCollection|NodeRelation[]
   */
  public function getIndirectRelations()
  {
    return $this->indirectRelations;
  }

}
