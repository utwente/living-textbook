<?php

namespace App\Entity;

use App\Controller\SearchController;
use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\ReviewableInterface;
use App\Entity\Contracts\SearchableInterface;
use App\Entity\Data\BaseDataTextObject;
use App\Entity\Data\DataExamples;
use App\Entity\Data\DataHowTo;
use App\Entity\Data\DataInterface;
use App\Entity\Data\DataIntroduction;
use App\Entity\Data\DataSelfAssessment;
use App\Entity\Data\DataTheoryExplanation;
use App\Entity\Traits\ReviewableTrait;
use App\Repository\ConceptRepository;
use App\Review\Exception\IncompatibleChangeException;
use App\Review\Exception\IncompatibleFieldChangedException;
use App\Validator\Constraint\ConceptRelation as ConceptRelationValidator;
use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMException;
use Drenso\Shared\Interfaces\IdInterface;
use Exception;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMSA\ExclusionPolicy("all")
 *
 * @ConceptRelationValidator()
 */
#[ORM\Entity(repositoryClass: ConceptRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table]
class Concept implements SearchableInterface, ReviewableInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"Default", "review_change", "name_only"})
   *
   * @JMSA\Type("string")
   */
  #[Assert\NotBlank]
  #[Assert\Length(min: 3, max: 255)]
  #[ORM\Column(name: 'name', length: 255, nullable: false)]
  private string $name = '';

  /**
   * Whether this concept should be seen as an instance.
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"Default", "review_change"})
   *
   * @JMSA\Type("boolean")
   */
  #[ORM\Column(name: 'instance')]
  private bool $instance = false;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type("string")
   */
  #[Assert\NotNull]
  #[ORM\Column(name: 'definition', type: Types::TEXT, nullable: false)]
  private string $definition = '';

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(DataIntroduction::class)
   */
  #[Assert\NotNull]
  #[Assert\Valid]
  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(name: 'introduction_id', referencedColumnName: 'id', nullable: false)]
  private DataIntroduction $introduction;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type("string")
   */
  #[Assert\NotNull]
  #[Assert\Length(max: 512)]
  #[ORM\Column(name: 'synonyms', length: 512, nullable: false)]
  private string $synonyms = '';

  /**
   * @var Collection<Concept>
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type("ArrayCollection<App\Entity\Concept>")
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[Assert\Valid]
  #[ORM\ManyToMany(targetEntity: Concept::class, inversedBy: 'priorKnowledgeOf')]
  #[ORM\JoinTable(name: 'concepts_prior_knowledge', joinColumns: [new ORM\JoinColumn(name: 'concept_id', referencedColumnName: 'id')], inverseJoinColumns: [new ORM\JoinColumn(name: 'prior_knowledge_id', referencedColumnName: 'id')])]
  #[ORM\OrderBy(['name' => 'ASC'])]
  private Collection $priorKnowledge;

  /** @var Collection<Concept> */
  #[ORM\ManyToMany(targetEntity: Concept::class, mappedBy: 'priorKnowledge')]
  private Collection $priorKnowledgeOf;

  /**
   * @var Collection<LearningOutcome>
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type("ArrayCollection<App\Entity\LearningOutcome>")
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[ORM\ManyToMany(targetEntity: LearningOutcome::class, inversedBy: 'concepts')]
  #[ORM\JoinTable(name: 'concepts_learning_outcomes', joinColumns: [new ORM\JoinColumn(name: 'concept_id', referencedColumnName: 'id')], inverseJoinColumns: [new ORM\JoinColumn(name: 'learning_outcome_id', referencedColumnName: 'id')])]
  #[ORM\OrderBy(['number' => 'ASC'])]
  private Collection $learningOutcomes;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(DataTheoryExplanation::class)
   */
  #[Assert\Valid]
  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(name: 'theory_explanation_id', referencedColumnName: 'id', nullable: false)]
  private DataTheoryExplanation $theoryExplanation;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(DataHowTo::class)
   */
  #[Assert\Valid]
  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(name: 'how_to_id', referencedColumnName: 'id', nullable: false)]
  private DataHowTo $howTo;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(DataExamples::class)
   */
  #[Assert\Valid]
  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(name: 'examples_id', referencedColumnName: 'id', nullable: false)]
  private DataExamples $examples;

  /**
   * @var Collection<ExternalResource>
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type("ArrayCollection<App\Entity\ExternalResource>")
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[ORM\ManyToMany(targetEntity: ExternalResource::class, inversedBy: 'concepts')]
  #[ORM\JoinTable(name: 'concepts_external_resources', joinColumns: [new ORM\JoinColumn(name: 'concept_id', referencedColumnName: 'id')], inverseJoinColumns: [new ORM\JoinColumn(name: 'external_resource_id', referencedColumnName: 'id')])]
  #[ORM\OrderBy(['title' => 'ASC'])]
  private Collection $externalResources;

  /**
   * @var Collection<Contributor>
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type("ArrayCollection<App\Entity\Contributor>")
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[ORM\ManyToMany(targetEntity: Contributor::class, inversedBy: 'concepts')]
  #[ORM\JoinTable(name: 'concepts_contributors', joinColumns: [new ORM\JoinColumn(name: 'concept_id', referencedColumnName: 'id')], inverseJoinColumns: [new ORM\JoinColumn(name: 'contributor_id', referencedColumnName: 'id')])]
  #[ORM\OrderBy(['name' => 'ASC'])]
  private Collection $contributors;

  /**
   * @var Collection<Tag>
   *
   * @JMSA\Expose()
   *
   * @JMSA\Type("ArrayCollection<App\Entity\Tag>")
   *
   * @JMSA\MaxDepth(2)
   */
  #[Assert\NotNull]
  #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'concepts')]
  private Collection $tags;

  /**
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(DataSelfAssessment::class)
   */
  #[Assert\Valid]
  #[ORM\OneToOne(cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(name: 'self_assessment_id', referencedColumnName: 'id', nullable: false)]
  private DataSelfAssessment $selfAssessment;

  /**
   * @var Collection<ConceptRelation>
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"relations", "review_change"})
   *
   * @JMSA\SerializedName("relations")
   *
   * @JMSA\Type("ArrayCollection<App\Entity\ConceptRelation>")
   *
   * @JMSA\MaxDepth(3)
   */
  #[Assert\Valid]
  #[Assert\NotNull]
  #[ORM\OneToMany(mappedBy: 'source', targetEntity: ConceptRelation::class, cascade: ['persist', 'remove'])]
  #[ORM\OrderBy(['outgoingPosition' => 'ASC'])]
  private Collection $outgoingRelations;

  /**
   * @var Collection<ConceptRelation>
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type("ArrayCollection<App\Entity\ConceptRelation>")
   *
   * @JMSA\MaxDepth(3)
   */
  #[Assert\Valid]
  #[Assert\NotNull]
  #[ORM\OneToMany(mappedBy: 'target', targetEntity: ConceptRelation::class, cascade: ['persist', 'remove'])]
  #[ORM\OrderBy(['incomingPosition' => 'ASC'])]
  private Collection $incomingRelations;

  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'concepts')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  #[ORM\Column(type: 'json', nullable: true)]
  private ?array $dotronConfig = null;

  public function __construct()
  {
    $this->outgoingRelations = new ArrayCollection();
    $this->incomingRelations = new ArrayCollection();

    // Prior knowledge
    $this->priorKnowledge   = new ArrayCollection();
    $this->priorKnowledgeOf = new ArrayCollection();

    // Learning outcome
    $this->learningOutcomes = new ArrayCollection();

    // External resources
    $this->externalResources = new ArrayCollection();

    // Contributors
    $this->contributors = new ArrayCollection();

    // Tags
    $this->tags = new ArrayCollection();

    // Initialize data
    $this->introduction      = new DataIntroduction();
    $this->theoryExplanation = new DataTheoryExplanation();
    $this->howTo             = new DataHowTo();
    $this->examples          = new DataExamples();
    $this->selfAssessment    = new DataSelfAssessment();
  }

  /** Check whether the relations have the correct owning data. */
  #[ORM\PreFlush]
  public function checkEntityRelations()
  {
    // Check relations
    foreach ($this->getOutgoingRelations() as $relation) {
      if ($relation->getSource() === null) {
        $relation->setSource($this);
      }
    }
    foreach ($this->getIncomingRelations() as $indirectRelation) {
      if ($indirectRelation->getTarget() === null) {
        $indirectRelation->setTarget($this);
      }
    }
  }

  /**
   * This method wil order the concept relations on flush.
   *
   * @noinspection PhpUnused
   *
   * @throws Exception
   */
  #[ORM\PreFlush]
  public function fixConceptRelationOrder()
  {
    $this->doFixConceptRelationOrder($this->getOutgoingRelations(), 'getTarget', 'setOutgoingPosition');
    $this->doFixConceptRelationOrder($this->getIncomingRelations(), 'getSource', 'setIncomingPosition');
  }

  /** @throws Exception */
  private function doFixConceptRelationOrder(Collection $values, string $conceptRetriever, string $positionSetter)
  {
    $iterator = $values->getIterator();
    assert($iterator instanceof ArrayIterator);
    $iterator->uasort(function (ConceptRelation $a, ConceptRelation $b) use ($conceptRetriever) {
      $val = strcasecmp($a->getRelationName(), $b->getRelationName());

      return $val === 0
          ? strcasecmp((string)$a->$conceptRetriever()->getName(), (string)$b->$conceptRetriever()->getName())
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
   * @JMSA\Expose()
   *
   * @JMSA\VirtualProperty()
   *
   * @JMSA\Groups({"relations"})
   *
   * @noinspection PhpUnused
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
    return $this->getDefinition() == '' && !$this->getIntroduction()->hasData();
  }

  /** @return bool */
  public function hasTextData()
  {
    return $this->getDefinition() != ''
        || $this->getIntroduction()->hasData()
        || $this->getExamples()->hasData()
        || $this->getHowTo()->hasData()
        || $this->selfAssessment->hasData()
        || $this->theoryExplanation->hasData();
  }

  /** @return array Array with DateTime and username */
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
    foreach ($this->getContributors() as $contributor) {
      $check($contributor);
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
    foreach ($this->getTags() as $tag) {
      $check($tag);
    }

    // Return result
    return [$lastUpdated, $lastUpdatedBy];
  }

  /** Searches in the concept on the given search, returns an array with search result metadata. */
  #[Override]
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if (stripos($this->getName(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'name', $this->getName());
    }

    if (stripos($this->getDefinition(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'definition', $this->getDefinition());
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
    if ($data->hasData() && stripos((string)$data->getText(), $search) !== false) {
      $results[] = SearchController::createResult($prio, $property, $data->getText());
    }
  }

  /**
   * @throws IncompatibleChangeException
   * @throws IncompatibleFieldChangedException
   * @throws ORMException
   */
  #[Override]
  public function applyChanges(PendingChange $change, EntityManagerInterface $em, bool $ignoreEm = false): void
  {
    $changeObj = $this->testChange($change);
    assert($changeObj instanceof self);

    foreach ($change->getChangedFields() as $changedField) {
      switch ($changedField) {
        case 'name':
          $this->setName($changeObj->getName());
          break;
        case 'instance':
          $this->setInstance($changeObj->isInstance());
          break;
        case 'definition':
          $this->setDefinition($changeObj->getDefinition());
          break;
        case 'introduction':
          $this->getIntroduction()->setText($changeObj->getIntroduction()->getText());
          break;
        case 'synonyms':
          $this->setSynonyms($changeObj->getSynonyms());
          break;
        case 'priorKnowledge':
          $this->getPriorKnowledge()->clear();

          foreach ($changeObj->getPriorKnowledge() as $newPriorKnowledge) {
            $newPriorKnowledgeRef = $em->getReference(self::class, $newPriorKnowledge->getId());
            assert($newPriorKnowledgeRef instanceof self);
            $this->addPriorKnowledge($newPriorKnowledgeRef);
          }
          break;

        case 'learningOutcomes':
          $this->getLearningOutcomes()->clear();

          foreach ($changeObj->getLearningOutcomes() as $newLearningOutcome) {
            $newLearningOutcomeRef = $em->getReference(LearningOutcome::class, $newLearningOutcome->getId());
            assert($newLearningOutcomeRef instanceof LearningOutcome);
            $this->addLearningOutcome($newLearningOutcomeRef);
          }
          break;

        case 'theoryExplanation':
          $this->getTheoryExplanation()->setText($changeObj->getTheoryExplanation()->getText());
          break;
        case 'howTo':
          $this->getHowTo()->setText($changeObj->getHowTo()->getText());
          break;
        case 'examples':
          $this->getExamples()->setText($changeObj->getExamples()->getText());
          break;
        case 'externalResources':
          $this->getExternalResources()->clear();

          foreach ($changeObj->getExternalResources() as $newExternalResource) {
            $newExternalResourceRef = $em->getReference(ExternalResource::class, $newExternalResource->getId());
            assert($newExternalResourceRef instanceof ExternalResource);
            $this->addExternalResource($newExternalResourceRef);
          }
          break;

        case 'contributors':
          $this->getContributors()->clear();

          foreach ($changeObj->getContributors() as $newContributor) {
            $newContributorRef = $em->getReference(Contributor::class, $newContributor->getId());
            assert($newContributorRef instanceof Contributor);
            $this->addContributor($newContributorRef);
          }
          break;

        case 'tags':
          $this->getTags()->clear();

          foreach ($changeObj->getTags() as $newTag) {
            $newTagRef = $em->getReference(Tag::class, $newTag->getId());
            assert($newTagRef instanceof Tag);
            $this->addTag($newTagRef);
          }
          break;

        case 'selfAssessment':
          $this->getSelfAssessment()->setText($changeObj->getSelfAssessment()->getText());
          break;
        case 'relations': // This would be outgoingRelations, but the serialized name is relations
          // This construct is required for Doctrine to work correctly. Why? No clue.
          $toRemove = [];
          foreach ($this->getOutgoingRelations() as $outgoingRelation) {
            $toRemove[] = $outgoingRelation;
          }
          foreach ($toRemove as $outgoingRelation) {
            $this->getOutgoingRelations()->removeElement($outgoingRelation);
            if (!$ignoreEm) {
              $em->remove($outgoingRelation);
            }
          }

          foreach ($changeObj->getOutgoingRelations() as $outgoingRelation) {
            $this->fixConceptRelationReferences($outgoingRelation, $em);

            $this->addOutgoingRelation($outgoingRelation);
            if (!$ignoreEm) {
              $em->persist($outgoingRelation);
            }
          }

          break;

        case 'incomingRelations':
          // This construct is required for Doctrine to work correctly. Why? No clue.
          $toRemove = [];
          foreach ($this->getIncomingRelations() as $incomingRelation) {
            $toRemove[] = $incomingRelation;
          }
          foreach ($toRemove as $incomingRelation) {
            $this->getIncomingRelations()->removeElement($incomingRelation);
            if (!$ignoreEm) {
              $em->remove($incomingRelation);
            }
          }

          foreach ($changeObj->getIncomingRelations() as $incomingRelation) {
            $this->fixConceptRelationReferences($incomingRelation, $em);

            $this->addIncomingRelation($incomingRelation);
            if (!$ignoreEm) {
              $em->persist($incomingRelation);
            }
          }

          break;

        default:
          throw new IncompatibleFieldChangedException($this, $changedField);
      }
    }
  }

  /**
   * Fixes the relations for use.
   *
   * @throws ORMException
   */
  private function fixConceptRelationReferences(ConceptRelation &$conceptRelation, EntityManagerInterface $em)
  {
    if ($conceptRelation->getSource()) {
      $sourceRef = $em->getReference(Concept::class, $conceptRelation->getSourceId());
      assert($sourceRef instanceof Concept);
      $conceptRelation->setSource($sourceRef);
    }

    if ($conceptRelation->getTarget()) {
      $targetRef = $em->getReference(Concept::class, $conceptRelation->getTargetId());
      assert($targetRef instanceof Concept);
      $conceptRelation->setTarget($targetRef);
    }

    $relationTypeRef = $em->getReference(RelationType::class, $conceptRelation->getRelationType()->getId());
    assert($relationTypeRef instanceof RelationType);
    $conceptRelation->setRelationType($relationTypeRef);
  }

  #[Override]
  public function getReviewTitle(): string
  {
    return $this->getName();
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function isInstance(): bool
  {
    return $this->instance;
  }

  public function setInstance(bool $instance): self
  {
    $this->instance = $instance;

    return $this;
  }

  public function setName(string $name): Concept
  {
    $this->name = $name;

    return $this;
  }

  public function getDefinition(): string
  {
    return $this->definition;
  }

  public function setDefinition(string $definition): Concept
  {
    $this->definition = $definition;

    return $this;
  }

  public function getSynonyms(): string
  {
    return $this->synonyms;
  }

  public function setSynonyms(string $synonyms): Concept
  {
    $this->synonyms = $synonyms;

    return $this;
  }

  public function getRelations()
  {
    return $this->getOutgoingRelations();
  }

  /** @return Collection<ConceptRelation> */
  public function getOutgoingRelations(): Collection
  {
    return $this->outgoingRelations;
  }

  public function addOutgoingRelation(ConceptRelation $conceptRelation): Concept
  {
    // Check whether the source is set, otherwise set it as this
    if (!$conceptRelation->getSource()) {
      $conceptRelation->setSource($this);
    }

    $this->outgoingRelations->add($conceptRelation);

    return $this;
  }

  public function removeOutgoingRelation(ConceptRelation $conceptRelation): Concept
  {
    $this->outgoingRelations->removeElement($conceptRelation);

    return $this;
  }

  /** @return Collection<ConceptRelation> */
  public function getIncomingRelations(): Collection
  {
    return $this->incomingRelations;
  }

  public function addIncomingRelation(ConceptRelation $conceptRelation): Concept
  {
    // Check whether the source is set, otherwise set it as this
    if (!$conceptRelation->getTarget()) {
      $conceptRelation->setTarget($this);
    }

    $this->incomingRelations->add($conceptRelation);

    return $this;
  }

  #[Override]
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  #[Override]
  public function setStudyArea(StudyArea $studyArea): Concept
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getIntroduction(): DataIntroduction
  {
    return $this->introduction;
  }

  public function setIntroduction(DataIntroduction $introduction): Concept
  {
    $this->introduction = $introduction;

    return $this;
  }

  public function getTheoryExplanation(): DataTheoryExplanation
  {
    return $this->theoryExplanation;
  }

  public function setTheoryExplanation(DataTheoryExplanation $theoryExplanation): Concept
  {
    $this->theoryExplanation = $theoryExplanation;

    return $this;
  }

  public function getHowTo(): DataHowTo
  {
    return $this->howTo;
  }

  public function setHowTo(DataHowTo $howTo): Concept
  {
    $this->howTo = $howTo;

    return $this;
  }

  public function getExamples(): DataExamples
  {
    return $this->examples;
  }

  public function setExamples(DataExamples $examples): Concept
  {
    $this->examples = $examples;

    return $this;
  }

  public function getSelfAssessment(): DataSelfAssessment
  {
    return $this->selfAssessment;
  }

  public function setSelfAssessment(DataSelfAssessment $selfAssessment): Concept
  {
    $this->selfAssessment = $selfAssessment;

    return $this;
  }

  /** @return Collection<ExternalResource> */
  public function getExternalResources(): Collection
  {
    return $this->externalResources;
  }

  public function addExternalResource(ExternalResource $externalResource): Concept
  {
    $this->externalResources->add($externalResource);

    return $this;
  }

  public function removeExternalResource(ExternalResource $externalResource): Concept
  {
    $this->externalResources->removeElement($externalResource);

    return $this;
  }

  /** @return Collection<Contributor> */
  public function getContributors(): Collection
  {
    return $this->contributors;
  }

  public function addContributor(Contributor $contributor): Concept
  {
    $this->contributors->add($contributor);

    return $this;
  }

  public function removeContributor(Contributor $contributor): Concept
  {
    $this->contributors->removeElement($contributor);

    return $this;
  }

  /** @return Collection<Concept> */
  public function getPriorKnowledge(): Collection
  {
    return $this->priorKnowledge;
  }

  public function addPriorKnowledge(Concept $concept): Concept
  {
    $this->priorKnowledge->add($concept);

    return $this;
  }

  public function removePriorKnowledge(Concept $concept): Concept
  {
    $this->priorKnowledge->removeElement($concept);

    return $this;
  }

  /** @return Collection<Concept> */
  public function getPriorKnowledgeOf(): Collection
  {
    return $this->priorKnowledgeOf;
  }

  /** @return Collection<LearningOutcome> */
  public function getLearningOutcomes(): Collection
  {
    return $this->learningOutcomes;
  }

  public function addLearningOutcome(LearningOutcome $learningOutcome): Concept
  {
    $this->learningOutcomes->add($learningOutcome);

    return $this;
  }

  public function removeLearningOutcome(LearningOutcome $learningOutcome): Concept
  {
    $this->learningOutcomes->removeElement($learningOutcome);

    return $this;
  }

  /** @return Collection<Tag> */
  public function getTags(): Collection
  {
    return $this->tags;
  }

  public function addTag(Tag $tag): Concept
  {
    if (!$this->tags->contains($tag)) {
      $this->tags->add($tag);
    }

    return $this;
  }

  public function removeTag(Tag $tag): Concept
  {
    $this->tags->removeElement($tag);

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
