<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="App\Repository\StylingConfigurationRepository")
 *
 * @ORM\HasLifecycleCallbacks()
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMSA\ExclusionPolicy("all")
 */
class StylingConfiguration implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="stylingConfigurations")
   *
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?StudyArea $studyArea = null;

  /** @ORM\Column(type="json", nullable=true) */
  private ?array $stylings = null;

  /**
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=255)
   *
   * @JMSA\Expose()
   */
  private string $name = '';

  /**
   * @var Collection<int, StylingConfigurationConceptOverride>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\StylingConfigurationConceptOverride", mappedBy="stylingConfiguration", fetch="EXTRA_LAZY", cascade={"remove"})
   */
  private Collection $conceptOverrides;

  /**
   * @var Collection<int, StylingConfigurationRelationOverride>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\StylingConfigurationRelationOverride", mappedBy="stylingConfiguration", fetch="EXTRA_LAZY", cascade={"remove"})
   */
  private Collection $relationOverrides;

  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(?StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getStylings(): ?array
  {
    return $this->stylings;
  }

  public function setStylings(?array $stylings): self
  {
    $this->stylings = $stylings;

    return $this;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  /** @return Collection<int, StylingConfigurationConceptOverride> */
  public function getConceptOverrides(): Collection
  {
    return $this->conceptOverrides;
  }

  /** @return Collection<int, StylingConfigurationRelationOverride> */
  public function getRelationOverrides(): Collection
  {
    return $this->relationOverrides;
  }
}
