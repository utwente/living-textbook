<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Data\DataExamples;
use App\Entity\Data\DataExternalResources;
use App\Entity\Data\DataHowTo;
use App\Entity\Data\DataIntroduction;
use App\Entity\Data\DataSelfAssessment;
use App\Entity\Data\DataTheoryExplanation;
use App\Validator\Constraint\ConceptRelation as ConceptRelationValidator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Concept
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ConceptRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 *
 * @ConceptRelationValidator()
 */
class Concept
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   * @Assert\Length(min=3, max=255)
   *
   * @JMSA\Expose()
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
   * @var Concept[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", inversedBy="priorKnowledgeOf")
   * @ORM\JoinTable(name="concepts_prior_knowledge",
   *      joinColumns={@ORM\JoinColumn(name="concept_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="prior_knowledge_id", referencedColumnName="id")}
   *      )
   * @ORM\OrderBy({"name" = "ASC"})
   *
   * @Assert\NotNull()
   * @Assert\Valid()
   */
  private $priorKnowledge;

  /**
   * @var Concept[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="priorKnowledge")
   */
  private $priorKnowledgeOf;

  /**
   * @var LearningOutcome[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\LearningOutcome", inversedBy="concepts")
   * @ORM\JoinTable(name="concepts_learning_outcomes",
   *      joinColumns={@ORM\JoinColumn(name="concept_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="learning_outcome_id", referencedColumnName="id")}
   *      )
   * @ORM\OrderBy({"number" = "ASC"})
   *
   * @Assert\NotNull()
   */
  private $learningOutcomes;

  /**
   * @var DataTheoryExplanation
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Data\DataTheoryExplanation", cascade={"persist","remove"})
   * @ORM\JoinColumn(name="theory_explanation_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\Valid()
   */
  private $theoryExplanation;

  /**
   * @var DataHowTo
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Data\DataHowTo", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="how_to_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\Valid()
   */
  private $howTo;

  /**
   * @var DataExamples
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Data\DataExamples", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="examples_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\Valid()
   */
  private $examples;

  /**
   * @var DataExternalResources
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Data\DataExternalResources", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="external_resources_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\Valid()
   */
  private $externalResources;

  /**
   * @var DataSelfAssessment
   *
   * @ORM\OneToOne(targetEntity="App\Entity\Data\DataSelfAssessment", cascade={"persist", "remove"})
   * @ORM\JoinColumn(name="self_assessment_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\Valid()
   */
  private $selfAssessment;

  /**
   * @var ArrayCollection|ConceptRelation[]
   *
   * @ORM\OneToMany(targetEntity="ConceptRelation", mappedBy="source", cascade={"persist","remove"})
   * @ORM\OrderBy({"outgoingPosition" = "ASC"})
   *
   * @Assert\NotNull()
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"relations"})
   * @JMSA\SerializedName("relations")
   */
  private $outgoingRelations;

  /**
   * @var ArrayCollection|ConceptRelation[]
   *
   * @ORM\OneToMany(targetEntity="ConceptRelation", mappedBy="target", cascade={"persist","remove"})
   * @ORM\OrderBy({"incomingPosition" = "ASC"})
   *
   * @Assert\NotNull()
   */
  private $incomingRelations;

  /**
   * @var StudyArea
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="concepts")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * Concept constructor.
   */
  public function __construct()
  {
    $this->name              = '';
    $this->outgoingRelations = new ArrayCollection();
    $this->incomingRelations = new ArrayCollection();

    // Prior knowledge
    $this->priorKnowledge   = new ArrayCollection();
    $this->priorKnowledgeOf = new ArrayCollection();

    // Learning outcome
    $this->learningOutcomes = new ArrayCollection();

    // Initialize data
    $this->introduction      = new DataIntroduction();
    $this->theoryExplanation = new DataTheoryExplanation();
    $this->howTo             = new DataHowTo();
    $this->examples          = new DataExamples();
    $this->selfAssessment    = new DataSelfAssessment();
    $this->externalResources = new DataExternalResources();
  }

  /**
   * Check whether the relations have the correct owning data
   *
   * @ORM\PreFlush()
   */
  public function checkEntityRelations()
  {
    // Check resources
    foreach ($this->getExternalResources()->getResources() as $resource) {
      if ($resource->getResourceCollection() === NULL) {
        $resource->setResourceCollection($this->getExternalResources());
      }
    };

    // Check relations
    foreach ($this->getOutgoingRelations() as $relation) {
      if ($relation->getSource() === NULL) {
        $relation->setSource($this);
      }
    }
    foreach ($this->getIncomingRelations() as $indirectRelation) {
      if ($indirectRelation->getTarget() === NULL) {
        $indirectRelation->setTarget($this);
      }
    }
  }

  /**
   * This method wil order the concept relations on flush
   *
   * @ORM\PreFlush()
   */
  public function fixConceptRelationOrder()
  {
    $this->doFixConceptRelationOrder($this->getOutgoingRelations(), 'getTarget', 'setOutgoingPosition');
    $this->doFixConceptRelationOrder($this->getIncomingRelations(), 'getSource', 'setIncomingPosition');
  }

  /**
   * @param Collection $values
   * @param string     $conceptRetriever
   * @param string     $positionSetter
   */
  private function doFixConceptRelationOrder(Collection $values, string $conceptRetriever, string $positionSetter)
  {
    $iterator = $values->getIterator();
    assert($iterator instanceof \ArrayIterator);
    $iterator->uasort(function (ConceptRelation $a, ConceptRelation $b) use ($conceptRetriever) {
      $val = strcasecmp($a->getRelationName(), $b->getRelationName());

      return $val === 0
          ? strcasecmp($a->$conceptRetriever()->getName(), $b->$conceptRetriever()->getName())
          : $val;
    });

    // Set sort order
    $key = 0;
    foreach (iterator_to_array($iterator) as &$value) {
      assert($value instanceof ConceptRelation);
      $value->$positionSetter($key);
      $key++;
    }
  }

  /**
   * @return int
   *
   * @JMSA\Expose()
   * @JMSA\VirtualProperty()
   * @JMSA\Groups({"relations","download_json"})
   */
  public function getNumberOfLinks(): int
  {
    return count($this->outgoingRelations) + count($this->incomingRelations);
  }

  /**
   * @return bool
   *
   * @JMSA\VirtualProperty()
   */
  public function isEmpty()
  {
    return !$this->getIntroduction()->hasData();
  }

  /**
   * @return string
   *
   * @JMSA\Expose()
   * @JMSA\VirtualProperty()
   * @JMSA\Groups({"download_json"})
   */
  public function getLabel()
  {
    return $this->getName();
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
   * @return ArrayCollection|ConceptRelation[]
   */
  public function getOutgoingRelations()
  {
    return $this->outgoingRelations;
  }

  /**
   * @param ConceptRelation $conceptRelation
   *
   * @return $this
   */
  public function addOutgoingRelation(ConceptRelation $conceptRelation): Concept
  {
    // Check whether the source is set, otherwise set it as this
    if (!$conceptRelation->getSource()) {
      $conceptRelation->setSource($this);
    }

    $this->outgoingRelations->add($conceptRelation);

    return $this;
  }

  /**
   * @param ConceptRelation $conceptRelation
   *
   * @return $this
   */
  public function removeOutgoingRelation(ConceptRelation $conceptRelation): Concept
  {
    $this->outgoingRelations->removeElement($conceptRelation);

    return $this;
  }

  /**
   * @return ArrayCollection|ConceptRelation[]
   */
  public function getIncomingRelations()
  {
    return $this->incomingRelations;
  }

  /**
   * @return StudyArea|null
   */
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return Concept
   */
  public function setStudyArea(StudyArea $studyArea): Concept
  {
    $this->studyArea = $studyArea;

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
   * @return DataTheoryExplanation
   */
  public function getTheoryExplanation(): DataTheoryExplanation
  {
    return $this->theoryExplanation;
  }

  /**
   * @param DataTheoryExplanation $theoryExplanation
   *
   * @return Concept
   */
  public function setTheoryExplanation(DataTheoryExplanation $theoryExplanation): Concept
  {
    $this->theoryExplanation = $theoryExplanation;

    return $this;
  }

  /**
   * @return DataHowTo
   */
  public function getHowTo(): DataHowTo
  {
    return $this->howTo;
  }

  /**
   * @param DataHowTo $howTo
   *
   * @return Concept
   */
  public function setHowTo(DataHowTo $howTo): Concept
  {
    $this->howTo = $howTo;

    return $this;
  }

  /**
   * @return DataExamples
   */
  public function getExamples(): DataExamples
  {
    return $this->examples;
  }

  /**
   * @param DataExamples $examples
   *
   * @return Concept
   */
  public function setExamples(DataExamples $examples): Concept
  {
    $this->examples = $examples;

    return $this;
  }

  /**
   * @return DataSelfAssessment
   */
  public function getSelfAssessment(): DataSelfAssessment
  {
    return $this->selfAssessment;
  }

  /**
   * @param DataSelfAssessment $selfAssessment
   *
   * @return Concept
   */
  public function setSelfAssessment(DataSelfAssessment $selfAssessment): Concept
  {
    $this->selfAssessment = $selfAssessment;

    return $this;
  }

  /**
   * @return DataExternalResources
   */
  public function getExternalResources(): DataExternalResources
  {
    return $this->externalResources;
  }

  /**
   * @param DataExternalResources $externalResources
   *
   * @return Concept
   */
  public function setExternalResources(DataExternalResources $externalResources): Concept
  {
    $this->externalResources = $externalResources;

    return $this;
  }

  /**
   * @return Concept[]|Collection
   */
  public function getPriorKnowledge()
  {
    return $this->priorKnowledge;
  }

  /**
   * @param Concept $concept
   *
   * @return Concept
   */
  public function addPriorKnowledge(Concept $concept): Concept
  {
    $this->priorKnowledge->add($concept);

    return $this;
  }

  /**
   * @param Concept $concept
   *
   * @return Concept
   */
  public function removePriorKnowledge(Concept $concept): Concept
  {
    $this->priorKnowledge->removeElement($concept);

    return $this;
  }

  /**
   * @return Concept[]|Collection
   */
  public function getPriorKnowledgeOf()
  {
    return $this->priorKnowledgeOf;
  }

  /**
   * @return LearningOutcome[]|Collection
   */
  public function getLearningOutcomes()
  {
    return $this->learningOutcomes;
  }

  /**
   * @param LearningOutcome $learningOutcome
   *
   * @return Concept
   */
  public function addLearningOurcome(LearningOutcome $learningOutcome): Concept
  {
    $this->learningOutcomes->add($learningOutcome);

    return $this;
  }

  /**
   * @param LearningOutcome $learningOutcome
   *
   * @return Concept
   */
  public function removeLearningOutcome(LearningOutcome $learningOutcome): Concept
  {
    $this->learningOutcomes->removeElement($learningOutcome);

    return $this;
  }
}
