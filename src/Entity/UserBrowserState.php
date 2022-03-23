<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class UserBrowserState
 * Holds information about the current browser state for the user, per study area.
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(columns={"user_id", "study_area_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\UserBrowserStateRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class UserBrowserState implements StudyAreaFilteredInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   *
   * @Assert\NotNull()
   */
  private $user;

  /**
   * @var StudyArea|null
   *
   * @ORM\ManyToOne(targetEntity="StudyArea")
   * @ORM\JoinColumn(nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * The current filter state.
   *
   * @var array
   *
   * @ORM\Column(type="json", nullable=true)
   */
  private $filterState;

  /** @return User|null */
  public function getUser(): ?User
  {
    return $this->user;
  }

  /** @return UserBrowserState */
  public function setUser(?User $user): self
  {
    $this->user = $user;

    return $this;
  }

  /** @return StudyArea|null */
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(StudyArea $studyArea): UserBrowserState
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  /** @return array|null */
  public function getFilterState(): ?array
  {
    return $this->filterState;
  }

  /** @return UserBrowserState */
  public function setFilterState(?array $filterState): self
  {
    $this->filterState = $filterState;

    return $this;
  }
}
