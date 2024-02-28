<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserBrowserState
 * Holds information about the current browser state for the user, per study area.
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"user_id", "study_area_id"})})
 *
 * @ORM\Entity(repositoryClass="App\Repository\UserBrowserStateRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class UserBrowserState implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   *
   * @ORM\JoinColumn(nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?User $user = null;

  /**
   * @ORM\ManyToOne(targetEntity="StudyArea")
   *
   * @ORM\JoinColumn(nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?StudyArea $studyArea = null;

  /**
   * The current filter state.
   *
   * @ORM\Column(type="json", nullable=true)
   */
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

  public function setStudyArea(StudyArea $studyArea): UserBrowserState
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
