<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudyAreaGroupRepository")
 */
class StudyAreaGroup
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @ORM\Column(type="string", length=255)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=5)
   */
  private $name;

  /**
   * @var StudyArea[]|ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="App\Entity\StudyArea", mappedBy="group", fetch="EXTRA_LAZY")
   */
  private $studyAreas;

  /**
   * If set the Dotron visualisation will be used
   *
   * @ORM\Column(type="boolean")
   */
  private bool $dotron = false;

  public function __construct()
  {
    $this->studyAreas = new ArrayCollection();
  }

  /**
   * Explicit count function for faster queries
   *
   * @return int
   */
  public function studyAreaCount(): int
  {
    return $this->studyAreas->count();
  }

  /**
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * @param string $name
   *
   * @return StudyAreaGroup
   */
  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return StudyArea[]|Collection
   */
  public function getStudyAreas()
  {
    return $this->studyAreas;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return self
   */
  public function addStudyArea(StudyArea $studyArea): self
  {
    // Check whether the group is set, otherwise set it as this
    if (!$studyArea->getGroup()) {
      $studyArea->setGroup($this);
    }
    $this->studyAreas->add($studyArea);

    return $this;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return self
   */
  public function removeStudyArea(StudyArea $studyArea): self
  {
    $this->studyAreas->removeElement($studyArea);
    $studyArea->setGroup(NULL);

    return $this;
  }

  public function isDotron(): bool
  {
    return $this->dotron;
  }

  public function setDotron(bool $dotron): self
  {
    $this->dotron = $dotron;

    return $this;
  }
}
