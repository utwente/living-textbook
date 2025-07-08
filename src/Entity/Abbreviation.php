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
use App\Repository\AbbreviationRepository;
use App\Review\Exception\IncompatibleChangeException;
use App\Review\Exception\IncompatibleFieldChangedException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

use function assert;
use function stripos;
use function trim;

#[ORM\Entity(repositoryClass: AbbreviationRepository::class)]
#[ORM\Table]
#[JMSA\ExclusionPolicy('all')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class Abbreviation implements SearchableInterface, StudyAreaFilteredInterface, ReviewableInterface, IdInterface
{
  use Blameable;
  use IdTrait;
  use ReviewableTrait;
  use SoftDeletable;

  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'abbreviations')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  #[Assert\NotBlank]
  #[Assert\Length(min: 1, max: 25)]
  #[ORM\Column(name: 'abbreviation', length: 25, nullable: false)]
  #[JMSA\Expose]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
  private string $abbreviation = '';

  #[Assert\NotBlank]
  #[Assert\Length(min: 1, max: 255)]
  #[ORM\Column(name: 'meaning', length: 255, nullable: false)]
  #[JMSA\Expose]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
  private string $meaning = '';

  /** Searches in the abbreviation on the given search, returns an array with search result metadata. */
  #[Override]
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if (stripos($this->getAbbreviation(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'abbreviation', $this->getAbbreviation());
    }

    if (stripos($this->getMeaning(), $search) !== false) {
      $results[] = SearchController::createResult(200, 'meaning', $this->getMeaning());
    }

    return [
      '_data'   => $this,
      '_title'  => $this->getAbbreviation(),
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
        'abbreviation' => $this->setAbbreviation($changeObj->getAbbreviation()),
        'meaning'      => $this->setMeaning($changeObj->getMeaning()),
        default        => throw new IncompatibleFieldChangedException($this, $changedField),
      };
    }
  }

  #[Override]
  public function getReviewTitle(): string
  {
    return $this->getAbbreviation();
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

  public function getAbbreviation(): string
  {
    return $this->abbreviation;
  }

  public function setAbbreviation(string $abbreviation): self
  {
    $this->abbreviation = trim($abbreviation);

    return $this;
  }

  public function getMeaning(): string
  {
    return $this->meaning;
  }

  public function setMeaning(string $meaning): self
  {
    $this->meaning = trim($meaning);

    return $this;
  }
}
