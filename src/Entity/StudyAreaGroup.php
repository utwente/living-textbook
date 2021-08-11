<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     attributes={},
 *     collectionOperations={"get", "post"={"security"="is_granted('ROLE_USER')"}},
 *     itemOperations={"get", "put"={"security"="is_granted('ROLE_USER') or object.owner == user"}},
 *     normalizationContext={"groups"={"studyareagroup:read"}},
 *     denormalizationContext={"groups"={"studyareagroup:write"}},
 * )
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
   * @Groups({"studyareagroup:read", "studyareagroup:write"})
   */
  private $name;

  /**
   * @var StudyArea[]|ArrayCollection
   *
   * @ORM\OneToMany(targetEntity="App\Entity\StudyArea", mappedBy="group", fetch="EXTRA_LAZY")
   * @Groups({"studyareagroup:read", "studyareagroup:write"})
   */
  private $studyAreas;

  /**
   * If set, GIMA students will be shown the Dorton Canvas
   *
   * @var bool
   *
   * @ORM\Column(name="is_dorton", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   * @Assert\Type("bool")
   * @Groups({"studyareagroup:read", "studyareagroup:write"})
   */
  private $isDorton;

  public function __construct()
  {
    $this->studyAreas = new ArrayCollection();
    $this->isDorton = false;
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
   * @return 0|bool
   */
  public function getIsDorton(): ?bool
  {
    return $this->isDorton;
  }


  /**
   * @param 0|bool $isDorton
   *
   * @return StudyArea
   */
  public function setIsDorton(bool $isDorton): self
  {
    $this->isDorton = $isDorton;

    return $this;
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
}
