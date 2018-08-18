<?php

namespace App\Entity;

use App\Controller\SearchController;
use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Data\BaseDataTextObject;
use App\Entity\Data\DataExamples;
use App\Entity\Data\DataHowTo;
use App\Entity\Data\DataInterface;
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
   * @var string
   *
   * @ORM\Column(name="synonyms", type="string", length=512, nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Length(max=512)
   */
  private $synonyms;

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
   * @var ExternalResource[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\ExternalResource", inversedBy="concepts")
   * @ORM\JoinTable(name="concepts_external_resources",
   *      joinColumns={@ORM\JoinColumn(name="concept_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="external_resource_id", referencedColumnName="id")}
   *      )
   * @ORM\OrderBy({"title" = "ASC"})
   *
   * @Assert\NotNull()
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
   * @Assert\Valid()
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
   * @Assert\Valid()
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
   * @var string|null
   *
   * @JMSA\Expose
   * @JMSA\Groups({"download_json"})
   */
  private $link;

  /**
   * Concept constructor.
   */
  public function __construct()
  {
    $this->name              = '';
    $this->synonyms          = '';
    $this->outgoingRelations = new ArrayCollection();
    $this->incomingRelations = new ArrayCollection();

    // Prior knowledge
    $this->priorKnowledge   = new ArrayCollection();
    $this->priorKnowledgeOf = new ArrayCollection();

    // Learning outcome
    $this->learningOutcomes = new ArrayCollection();

    // External resources
    $this->externalResources = new ArrayCollection();

    // Initialize data
    $this->introduction      = new DataIntroduction();
    $this->theoryExplanation = new DataTheoryExplanation();
    $this->howTo             = new DataHowTo();
    $this->examples          = new DataExamples();
    $this->selfAssessment    = new DataSelfAssessment();
  }

  /**
   * Check whether the relations have the correct owning data
   *
   * @ORM\PreFlush()
   */
  public function checkEntityRelations()
  {
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
   * @return bool
   */
  public function hasTextData()
  {
    return $this->getIntroduction()->hasData()
        || $this->getExamples()->hasData()
        || $this->getHowTo()->hasData()
        || $this->selfAssessment->hasData()
        || $this->theoryExplanation->hasData();
  }

  /**
   * @return array Array with DateTime and username
   */
  public function getLastEditInfo()
  {
    $lastUpdated   = $this->getLastUpdated();
    $lastUpdatedBy = $this->getLastUpdatedBy();

    // Loop relations to see if they have a newer date set
    $check = function ($entity) use (&$lastUpdated, &$lastUpdatedBy) {
      /** @var Blameable $entity */
      if ($entity->getLastUpdated() > $lastUpdated) {
        $lastUpdated   = $entity->getLastUpdated();
        $lastUpdatedBy = $entity->getLastUpdatedBy();
      }
    };

    // Check direct data
    $check($this->getExamples());
    $check($this->getHowTo());
    $check($this->getIntroduction());
    $check($this->getSelfAssessment());
    $check($this->getTheoryExplanation());

    // Check other data
    foreach ($this->getExternalResources() as $externalResource) {
      $check($externalResource);
    }
    foreach ($this->getIncomingRelations() as $incomingRelation) {
      $check($incomingRelation);
    }
    foreach ($this->getLearningOutcomes() as $learningOutcome) {
      $check($learningOutcome);
    }
    foreach ($this->getOutgoingRelations() as $outgoingRelation) {
      $check($outgoingRelation);
    }

    // Return result
    return [$lastUpdated, $lastUpdatedBy];
  }

  /**
   * Searches in the concept on the given search, returns an array with search result metadata
   *
   * @param string $search
   *
   * @return array
   */
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if (stripos($this->getName(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'name', $this->getName());
    }

    if (stripos($this->getSynonyms(), $search) !== false) {
      $results[] = SearchController::createResult(200, 'synonyms', $this->getSynonyms());
    }

    $this->filterDataOn($results, $this->getIntroduction(), 150, 'introduction', $search);
    $this->filterDataOn($results, $this->getExamples(), 100, 'examples', $search);
    $this->filterDataOn($results, $this->getTheoryExplanation(), 80, 'theory-explanation', $search);
    $this->filterDataOn($results, $this->getHowTo(), 60, 'how-to', $search);
    $this->filterDataOn($results, $this->getSelfAssessment(), 40, 'self-assessment', $search);

    return [
        '_id'     => $this->getId(),
        '_title'  => $this->getName(),
        'results' => $results,
    ];
  }

  private function filterDataOn(array &$results, DataInterface $data, int $prio, string $property, string $search)
  {
    assert($data instanceof BaseDataTextObject);
    if ($data->hasData() && stripos($data->getText(), $search) !== false) {
      $results[] = SearchController::createResult($prio, $property, $data->getText());
    }
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
   * @return string
   */
  public function getSynonyms(): string
  {
    return $this->synonyms;
  }

  /**
   * @param string $synonyms
   */
  public function setSynonyms(string $synonyms): void
  {
    $this->synonyms = $synonyms;
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
   * @return ExternalResource[]|Collection
   */
  public function getExternalResources()
  {
    return $this->externalResources;
  }


  /**
   * @param ExternalResource $externalResource
   *
   * @return Concept
   */
  public function addExternalResource(ExternalResource $externalResource): Concept
  {
    $this->externalResources->add($externalResource);

    return $this;
  }

  /**
   * @param ExternalResource $externalResource
   *
   * @return Concept
   */
  public function removeExternalResource(ExternalResource $externalResource): Concept
  {
    $this->externalResources->removeElement($externalResource);

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
  public function addLearningOutcome(LearningOutcome $learningOutcome): Concept
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

  /**
   * @param string $link
   *
   * @return Concept
   */
  public function setLink(string $link): Concept
  {
    $this->link = $link;

    return $this;
  }

  /**
   * @return null|string
   * @internal Used for serialization only
   */
  public function getLink(): ?string
  {
    return $this->link;
  }
}
