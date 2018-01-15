<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\ClassConstraints\ConceptClass;

/**
 * Class Concept
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ConceptRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMS\ExclusionPolicy("all")
 *
 * @ConceptClass()
 */
class Concept
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
   * @var ArrayCollection|ConceptRelation[]
   *
   * @ORM\OneToMany(targetEntity="ConceptRelation", mappedBy="source", cascade={"persist","remove"}, fetch="EAGER")
   *
   * @Assert\NotNull()
   *
   * @JMS\Expose()
   * @JMS\Groups({"relations"})
   */
  private $relations;

  /**
   * @var ArrayCollection|ConceptRelation[]
   *
   * @ORM\OneToMany(targetEntity="ConceptRelation", mappedBy="target", cascade={"persist","remove"}, fetch="EAGER")
   *
   * @Assert\NotNull()
   */
  private $indirectRelations;

  /**
   * @var ArrayCollection|ConceptStudyArea[]
   *
   * @ORM\OneToMany(targetEntity="ConceptStudyArea", mappedBy="concept", cascade={"persist","remove"})
   *
   * @Assert\NotNull()
   */
  private $studyAreas;

  /**
   * Concept constructor.
   */
  public function __construct()
  {
    $this->relations         = new ArrayCollection();
    $this->indirectRelations = new ArrayCollection();
    $this->studyAreas        = new ArrayCollection();
  }

  /**
   * @return int
   *
   * @JMS\VirtualProperty("numberOfLinks")
   * @JMS\Groups({"relations"})
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
   * @return Concept
   */
  public function setName(string $name): Concept
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return ArrayCollection|Concept[]
   */
  public function getRelations()
  {
    return $this->relations;
  }

  /**
   * @param ConceptRelation $conceptRelation
   *
   * @return $this
   */
  public function addRelation(ConceptRelation $conceptRelation): Concept
  {
    // Check whether the source is set, otherwise set it as this
    if (!$conceptRelation->getSource()) {
      $conceptRelation->setSource($this);
    }

    $this->relations->add($conceptRelation);

    return $this;
  }

  /**
   * @param ConceptRelation $conceptRelation
   *
   * @return $this
   */
  public function removeRelation(ConceptRelation $conceptRelation): Concept
  {
    $this->relations->removeElement($conceptRelation);

    return $this;
  }

  /**
   * @return ArrayCollection|ConceptRelation[]
   */
  public function getIndirectRelations()
  {
    return $this->indirectRelations;
  }

  /**
   * @return ConceptStudyArea[]|ArrayCollection
   */
  public function getStudyAreas()
  {
    return $this->studyAreas;
  }

  /**
   * @param ConceptStudyArea $studyArea
   *
   * @return $this
   */
  public function addStudyArea(ConceptStudyArea $studyArea): Concept
  {
    // Check whether the concept is set, otherwise set it as this
    if (!$studyArea->getConcept()) {
      $studyArea->setConcept($this);
    }

    $this->studyAreas->add($studyArea);

    return $this;
  }

  /**
   * @param ConceptStudyArea $studyArea
   *
   * @return $this
   */
  public function removeStudyArea(ConceptStudyArea $studyArea): Concept
  {
    $this->studyAreas->removeElement($studyArea);

    return $this;
  }

}
