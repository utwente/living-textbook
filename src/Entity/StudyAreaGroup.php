<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Repository\StudyAreaGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudyAreaGroupRepository::class)]
class StudyAreaGroup implements IdInterface
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  #[Assert\NotBlank]
  #[Assert\Length(min: 5)]
  #[ORM\Column(length: 255)]
  private ?string $name = null;

  /** @var Collection<StudyArea> */
  #[ORM\OneToMany(mappedBy: 'group', targetEntity: StudyArea::class, fetch: 'EXTRA_LAZY')]
  private Collection $studyAreas;

  public function __construct()
  {
    $this->studyAreas = new ArrayCollection();
  }

  /** Explicit count function for faster queries. */
  public function studyAreaCount(): int
  {
    return $this->studyAreas->count();
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  /** @return Collection<StudyArea> */
  public function getStudyAreas(): Collection
  {
    return $this->studyAreas;
  }

  public function addStudyArea(StudyArea $studyArea): self
  {
    // Check whether the group is set, otherwise set it as this
    if (!$studyArea->getGroup()) {
      $studyArea->setGroup($this);
    }
    $this->studyAreas->add($studyArea);

    return $this;
  }

  public function removeStudyArea(StudyArea $studyArea): self
  {
    $this->studyAreas->removeElement($studyArea);
    $studyArea->setGroup(null);

    return $this;
  }
}
