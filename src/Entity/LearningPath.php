<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\ReviewableInterface;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\Traits\ReviewableTrait;
use App\Repository\LearningPathRepository;
use App\Review\Exception\IncompatibleChangeException;
use App\Review\Exception\IncompatibleFieldChangedException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMException;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

use function array_reverse;
use function assert;
use function count;
use function end;

#[ORM\Entity(repositoryClass: LearningPathRepository::class)]
#[ORM\Table]
#[JMSA\ExclusionPolicy('all')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class LearningPath implements StudyAreaFilteredInterface, ReviewableInterface, IdInterface
{
  use Blameable;
  use IdTrait;
  use ReviewableTrait;
  use SoftDeletable;

  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'learningPaths')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  /** Learning path name. */
  #[Assert\NotBlank]
  #[Assert\Length(max: 255)]
  #[ORM\Column(name: 'name', length: 255, nullable: false)]
  #[JMSA\Expose]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
  private string $name = '';

  /** Learning path introduction. */
  #[Assert\NotBlank]
  #[ORM\Column(name: 'introduction', type: Types::TEXT, nullable: true)]
  #[JMSA\Expose]
  #[JMSA\Groups(['review_change'])]
  #[JMSA\Type('string')] // ;
  private ?string $introduction = null;

  /** Learning path question. */
  #[Assert\NotBlank]
  #[Assert\Length(max: 1024)]
  #[ORM\Column(name: 'question', length: 1024, nullable: false)]
  #[JMSA\Expose]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
  private string $question = '';

  /** @var Collection<LearningPathElement> */
  #[Assert\Valid]
  #[ORM\OneToMany(mappedBy: 'learningPath', targetEntity: LearningPathElement::class, cascade: ['persist', 'remove'])]
  #[JMSA\Expose]
  #[JMSA\Groups(['review_change'])]
  #[JMSA\Type('ArrayCollection<App\Entity\LearningPathElement>')]
  private Collection $elements;

  public function __construct()
  {
    $this->elements = new ArrayCollection();
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
        case 'introduction':
          $this->setIntroduction($changeObj->getIntroduction());
          break;
        case 'question':
          $this->setQuestion($changeObj->getQuestion());
          break;
        case 'elements':
          // This construct is required for Doctrine to work correctly. Why? No clue.
          $toRemove = [];
          foreach ($this->getElements() as $element) {
            $toRemove[] = $element;
          }
          foreach ($toRemove as $element) {
            $this->getElements()->removeElement($element);
            if (!$ignoreEm) {
              $em->remove($element);
            }
          }

          // Set the new elements
          foreach ($changeObj->getElements() as $newElement) {
            $conceptRef = $em->getReference(Concept::class, $newElement->getConcept()->getId());
            assert($conceptRef instanceof Concept);
            $newElement->setConcept($conceptRef);

            $this->addElement($newElement);
            if (!$ignoreEm) {
              $em->persist($newElement);
            }
          }
          break;

        default:
          throw new IncompatibleFieldChangedException($this, $changedField);
      }
    }
  }

  #[Override]
  public function getReviewTitle(): string
  {
    return $this->getName();
  }

  #[Override]
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  #[Override]
  public function setStudyArea(StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getQuestion(): string
  {
    return $this->question;
  }

  public function setQuestion(string $question): self
  {
    $this->question = $question;

    return $this;
  }

  public function getIntroduction(): ?string
  {
    return $this->introduction;
  }

  public function setIntroduction(?string $introduction): self
  {
    $this->introduction = $introduction;

    return $this;
  }

  /**
   * Retrieve the ordered element list.
   *
   * @return Collection<LearningPathElement>
   */
  public function getElements(): Collection
  {
    return $this->elements;
  }

  /**
   * Get the elements ordered.
   *
   * @return Collection<LearningPathElement>
   */
  #[JMSA\Expose]
  #[JMSA\VirtualProperty]
  #[JMSA\SerializedName('elements')]
  public function getElementsOrdered(): Collection
  {
    return self::OrderElements($this->elements);
  }

  /**
   * Get the elements ordered.
   *
   * @param Collection<LearningPathElement> $elements
   *
   * @return Collection<LearningPathElement>
   */
  public static function OrderElements(Collection $elements): Collection
  {
    $result      = [];
    $mappingNext = [];
    foreach ($elements as $element) {
      if ($element->getNext()) {
        $mappingNext[$element->getNext()->getId()] = $element;
      } else {
        // No next, so is last element
        $result[] = $element;
      }
    }

    while (count($mappingNext) > 0) {
      $nextId   = end($result)->getId();
      $result[] = $mappingNext[$nextId];
      unset($mappingNext[$nextId]);
    }

    return new ArrayCollection(array_reverse($result));
  }

  public function addElement(LearningPathElement $element): self
  {
    if (!$element->getLearningPath()) {
      $element->setLearningPath($this);
    }

    $this->elements->add($element);

    return $this;
  }

  public function removeElement(LearningPathElement $element): self
  {
    $this->elements->removeElement($element);

    return $this;
  }
}
