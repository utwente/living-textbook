<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\SoftDeletable;
use App\Entity\Data\DataIntroduction;
use App\Entity\Data\DataLearningOutcomes;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

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
   * @var DataIntroduction
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Data\DataIntroduction", cascade={"persist","remove"})
   * @ORM\JoinColumn(name="introduction_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Valid()
   */
  private $introduction;

  /**
   * @var DataLearningOutcomes
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Data\DataLearningOutcomes", cascade={"persist","remove"})
   * @ORM\JoinColumn(name="learning_outcomes_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\Valid()
   */
  private $learningOutcomes;

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
   * @Assert\Count(min=1, minMessage="concept.no-study-area-given")
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

    // Initialize data
    $this->introduction     = new DataIntroduction();
    $this->learningOutcomes = new DataLearningOutcomes();
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

  /**
   * @return DataIntroduction
   */
  public function getIntroduction(): DataIntroduction
  {
    return $this->introduction;
  }

  /**
   * @param DataIntroduction $introduction
   *
   * @return Concept
   */
  public function setIntroduction(DataIntroduction $introduction): Concept
  {
    $this->introduction = $introduction;

    return $this;
  }

  /**
   * @return DataLearningOutcomes
   */
  public function getLearningOutcomes(): DataLearningOutcomes
  {
    return $this->learningOutcomes;
  }

  /**
   * @param DataLearningOutcomes $learningOutcomes
   *
   * @return Concept
   */
  public function setLearningOutcomes(DataLearningOutcomes $learningOutcomes): Concept
  {
    $this->learningOutcomes = $learningOutcomes;

    return $this;
  }

}
