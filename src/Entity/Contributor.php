<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\ReviewableInterface;
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
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Contributor.
 *
 * @ORM\Entity(repositoryClass="App\Repository\ContributorRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Contributor implements StudyAreaFilteredInterface, ReviewableInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

  /**
   * @var Collection<Concept>
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="contributors")
   */
  private Collection $concepts;

  /**
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="contributors")
   *
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?StudyArea $studyArea = null;

  /**
   * @ORM\Column(name="name", type="string", length=512, nullable=false)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Length(min=1, max=512)
   *
   * @JMSA\Groups({"Default", "review_change"})
   *
   * @JMSA\Type("string")
   */
  private string $name = '';

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
   * @ORM\Column(name="email", type="string", length=255, nullable=true)
   *
   * @Assert\Email()
   *
   * @Assert\Length(max=255)
   *
   * @JMSA\Groups({"Default", "review_change"})
   *
   * @JMSA\Type("string")
   */
  private ?string $email = null;

  /**
   * @ORM\Column(name="broken", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   */
  private bool $broken = false;

  /** Contributor constructor. */
  public function __construct()
  {
    $this->concepts = new ArrayCollection();
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
        'name'        => $this->setName($changeObj->getName()),
        'description' => $this->setDescription($changeObj->getDescription()),
        'url'         => $this->setUrl($changeObj->getUrl()),
        default       => throw new IncompatibleFieldChangedException($this, $changedField),
      };
    }
  }

  #[Override]
  public function getReviewTitle(): string
  {
    return $this->getName();
  }

  /** @return Collection<Concept> */
  public function getConcepts(): Collection
  {
    return $this->concepts;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = trim($name);

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): Contributor
  {
    $this->description = StringHelper::emptyToNull($description);

    return $this;
  }

  public function getUrl(): ?string
  {
    return $this->url;
  }

  public function setUrl(?string $url): Contributor
  {
    $this->url = StringHelper::emptyToNull($url);

    return $this;
  }

  public function isBroken(): bool
  {
    return $this->broken;
  }

  public function setBroken(bool $broken): Contributor
  {
    $this->broken = $broken;

    return $this;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

  public function setEmail(?string $email): Contributor
  {
    $this->email = $email;

    return $this;
  }

  #[Override]
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  #[Override]
  public function setStudyArea(StudyArea $studyArea): Contributor
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
