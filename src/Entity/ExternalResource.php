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
use App\Repository\ExternalResourceRepository;
use App\Review\Exception\IncompatibleChangeException;
use App\Review\Exception\IncompatibleFieldChangedException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Helper\StringHelper;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

use function assert;
use function stripos;

#[ORM\Entity(repositoryClass: ExternalResourceRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class ExternalResource implements SearchableInterface, StudyAreaFilteredInterface, ReviewableInterface, IdInterface
{
  use Blameable;
  use IdTrait;
  use ReviewableTrait;
  use SoftDeletable;

  /** @var Collection<Concept> */
  #[ORM\ManyToMany(targetEntity: Concept::class, mappedBy: 'externalResources')]
  private Collection $concepts;

  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'externalResources')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  #[Assert\Length(min: 1, max: 512)]
  #[ORM\Column(name: 'title', length: 512, nullable: true)]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
  private ?string $title = null;

  #[Assert\Length(max: 1024)]
  #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
  private ?string $description = null;

  #[Assert\Url]
  #[Assert\Length(max: 512)]
  #[ORM\Column(name: 'url', length: 512, nullable: true)]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
  private ?string $url = null;

  #[Assert\NotNull]
  #[ORM\Column(name: 'broken', nullable: false)]
  private bool $broken = false;

  public function __construct()
  {
    $this->concepts = new ArrayCollection();
  }

  #[Assert\Callback]
  public function validateObject(ExecutionContextInterface $context): void
  {
    // Either title or url must be set
    if (!$this->title && !$this->url) {
      $context
        ->buildViolation('external-resource.title-or-url')
        ->atPath('title')
        ->addViolation();
    }
  }

  /** Searches in the external resource on the given search, returns an array with search result metadata. */
  #[Override]
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if ($this->getTitle() && stripos($this->getTitle(), $search) !== false) {
      $results[] = SearchController::createResult(255, 'title', $this->getTitle());
    }
    if ($this->getDescription() && stripos($this->getDescription(), $search) !== false) {
      $results[] = SearchController::createResult(200, 'description', $this->getDescription());
    }
    if ($this->getUrl() && stripos($this->getUrl(), $search) !== false) {
      $results[] = SearchController::createResult(150, 'url', $this->getUrl());
    }

    return [
      '_data'   => $this,
      '_title'  => $this->getTitleOrUrl(),
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
    /** @var self $changeObj */
    $changeObj = $this->testChange($change);

    foreach ($change->getChangedFields() as $changedField) {
      match ($changedField) {
        'title'       => $this->setTitle($changeObj->getTitle()),
        'description' => $this->setDescription($changeObj->getDescription()),
        'url'         => $this->setUrl($changeObj->getUrl()),
        default       => throw new IncompatibleFieldChangedException($this, $changedField),
      };
    }
  }

  #[Override]
  public function getReviewTitle(): string
  {
    return $this->getTitleOrUrl();
  }

  /** @return Collection<int, Concept> */
  public function getConcepts(): Collection
  {
    return $this->concepts;
  }

  public function getTitleOrUrl(): string
  {
    return $this->title ?? $this->url ?? '<no-title>';
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(?string $title): self
  {
    $this->title = StringHelper::emptyToNull($title);

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = StringHelper::emptyToNull($description);

    return $this;
  }

  public function getUrl(): ?string
  {
    return $this->url;
  }

  public function setUrl(?string $url): self
  {
    $this->url = StringHelper::emptyToNull($url);

    return $this;
  }

  public function isBroken(): bool
  {
    return $this->broken;
  }

  public function setBroken(bool $broken): self
  {
    $this->broken = $broken;

    return $this;
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
}
