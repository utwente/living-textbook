<?php

namespace App\Entity;

use App\Controller\SearchController;
use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\ReviewableInterface;
use App\Entity\Contracts\SearchableInterface;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\Traits\ReviewableTrait;
use App\Repository\LearningOutcomeRepository;
use App\Review\Exception\IncompatibleChangeException;
use App\Review\Exception\IncompatibleFieldChangedException;
use App\Validator\Constraint\Data\WordCount;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Override;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LearningOutcome.
 *
 * @author BobV
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
#[UniqueEntity(fields: ['studyArea', 'number'], errorPath: 'number', message: 'learning-outcome.number-already-used')]
#[ORM\Entity(repositoryClass: LearningOutcomeRepository::class)]
#[ORM\Table]
class LearningOutcome implements SearchableInterface, StudyAreaFilteredInterface, ReviewableInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

  /** @var Collection<Concept> */
  #[ORM\ManyToMany(targetEntity: Concept::class, mappedBy: 'learningOutcomes')]
  private Collection $concepts;

  #[Assert\NotNull]
  #[ORM\ManyToOne(targetEntity: \StudyArea::class, inversedBy: 'learningOutcomes')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  /** Learning outcome number. */
  #[Assert\NotBlank]
  #[Assert\Range(min: '1', max: '9999')]
  #[ORM\Column(name: 'number', type: 'integer', nullable: false)]
  #[Serializer\Groups(['Default', 'review_change'])]
  #[Serializer\Type('int')]
  private int $number = 1;

  /** Learning outcome name. */
  #[Assert\NotBlank]
  #[Assert\Length(max: '255')]
  #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
  #[Serializer\Groups(['Default', 'review_change'])]
  #[Serializer\Type('string')]
  private string $name = '';

  /**
   * Learning outcome text.
   *
   * @WordCount(min=1, max=10000)
   */
  #[Assert\NotBlank]
  #[ORM\Column(name: 'text', type: 'text', nullable: false)]
  #[Serializer\Groups(['Default', 'review_change'])]
  #[Serializer\Type('string')]
  private string $text = '';

  public function __construct()
  {
    $this->concepts = new ArrayCollection();
  }

  public function getShortName()
  {
    return sprintf('%d - %s', $this->number, $this->name);
  }

  /** Searches in the external resource on the given search, returns an array with search result metadata. */
  #[Override]
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if (stripos($this->getName(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'name', $this->getName());
    }
    if (stripos($this->getText(), $search) !== false) {
      $results[] = SearchController::createResult(200, 'text', $this->getText());
    }

    return [
      '_id'     => $this->getId(),
      '_title'  => $this->getName(),
      'results' => $results,
    ];
  }

  /**
   * @throws IncompatibleChangeException
   * @throws IncompatibleFieldChangedException
   */
  #[Override]
  public function applyChanges(PendingChange $change, EntityManagerInterface $em, bool $ignoreEm = false): void
  {
    $changeObj = $this->testChange($change);
    assert($changeObj instanceof self);

    foreach ($change->getChangedFields() as $changedField) {
      match ($changedField) {
        'number' => $this->setNumber($changeObj->getNumber()),
        'name'   => $this->setName($changeObj->getName()),
        'text'   => $this->setText($changeObj->getText()),
        default  => throw new IncompatibleFieldChangedException($this, $changedField),
      };
    }
  }

  #[Override]
  public function getReviewTitle(): string
  {
    return $this->getName();
  }

  public function getNumber(): int
  {
    return $this->number;
  }

  public function setNumber(int $number): LearningOutcome
  {
    $this->number = $number;

    return $this;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): LearningOutcome
  {
    $this->name = trim($name);

    return $this;
  }

  public function getText(): string
  {
    return $this->text;
  }

  public function setText(string $text): LearningOutcome
  {
    $this->text = trim($text);

    return $this;
  }

  /** @return Collection<Concept> */
  public function getConcepts(): Collection
  {
    return $this->concepts;
  }

  #[Override]
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  #[Override]
  public function setStudyArea(StudyArea $studyArea): LearningOutcome
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
