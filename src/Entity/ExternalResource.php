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
use App\Review\Exception\IncompatibleChangeException;
use App\Review\Exception\IncompatibleFieldChangedException;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Helper\StringHelper;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExternalResource.
 *
 * @author BobV
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="App\Repository\ExternalResourceRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class ExternalResource implements SearchableInterface, StudyAreaFilteredInterface, ReviewableInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

  /**
   * @var Collection<Concept>
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="externalResources")
   */
  private Collection $concepts;

  /**
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="externalResources")
   *
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?StudyArea $studyArea = null;

  /**
   * @ORM\Column(name="title", type="string", length=512, nullable=false)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Length(min=1, max=512)
   *
   * @JMSA\Groups({"Default", "review_change"})
   *
   * @JMSA\Type("string")
   */
  private string $title = '';

  /**
   * @ORM\Column(name="description", type="text", nullable=true)
   *
   * @Assert\Length(max=1024)
   *
   * @JMSA\Groups({"Default", "review_change"})
   *
   * @JMSA\Type("string")
   */
  private ?string $description = null;

  /**
   * @ORM\Column(name="url", type="string", length=512, nullable=true)
   *
   * @Assert\Url()
   *
   * @Assert\Length(max=512)
   *
   * @JMSA\Groups({"Default", "review_change"})
   *
   * @JMSA\Type("string")
   */
  private ?string $url = null;

  /**
   * @ORM\Column(name="broken", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   */
  private bool $broken = false;

  /** ExternalResource constructor. */
  public function __construct()
  {
    $this->concepts = new ArrayCollection();
  }

  /** Searches in the external resource on the given search, returns an array with search result metadata. */
  public function searchIn(string $search): array
  {
    // Create result array
    $results = [];

    // Search in different parts
    if (stripos($this->getTitle(), $search) !== false) {
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
      '_title'  => $this->getTitle(),
      'results' => $results,
    ];
  }

  /**
   * @throws IncompatibleChangeException
   * @throws IncompatibleFieldChangedException
   */
  public function applyChanges(PendingChange $change, EntityManagerInterface $em, bool $ignoreEm = false): void
  {
    $changeObj = $this->testChange($change);
    assert($changeObj instanceof self);

    foreach ($change->getChangedFields() as $changedField) {
      match ($changedField) {
        'title'       => $this->setTitle($changeObj->getTitle()),
        'description' => $this->setDescription($changeObj->getDescription()),
        'url'         => $this->setUrl($changeObj->getUrl()),
        default       => throw new IncompatibleFieldChangedException($this, $changedField),
      };
    }
  }

  public function getReviewTitle(): string
  {
    return $this->getTitle();
  }

  /** @return Collection<Concept> */
  public function getConcepts(): Collection
  {
    return $this->concepts;
  }

  public function getTitle(): string
  {
    return $this->title;
  }

  public function setTitle(string $title): ExternalResource
  {
    $this->title = trim($title);

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): ExternalResource
  {
    $this->description = StringHelper::emptyToNull($description);

    return $this;
  }

  public function getUrl(): ?string
  {
    return $this->url;
  }

  public function setUrl(?string $url): ExternalResource
  {
    $this->url = StringHelper::emptyToNull($url);

    return $this;
  }

  public function isBroken(): bool
  {
    return $this->broken;
  }

  public function setBroken(bool $broken): ExternalResource
  {
    $this->broken = $broken;

    return $this;
  }

  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(StudyArea $studyArea): ExternalResource
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
