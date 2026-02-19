<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Repository\UserBrowserStateRepository;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/** Holds information about the current browser state for the user, per study area. */
#[ORM\Entity(repositoryClass: UserBrowserStateRepository::class)]
#[ORM\Table]
#[ORM\UniqueConstraint(columns: ['user_id', 'study_area_id'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class UserBrowserState implements StudyAreaFilteredInterface, IdInterface
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?StudyArea $studyArea = null;

  /** The current filter state. */
  #[ORM\Column(nullable: true)]
  private ?array $filterState = null;

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  #[Override]
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getFilterState(): ?array
  {
    return $this->filterState;
  }

  public function setFilterState(?array $filterState): self
  {
    $this->filterState = $filterState;

    return $this;
  }
}
