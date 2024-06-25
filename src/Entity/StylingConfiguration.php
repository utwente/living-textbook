<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Repository\StylingConfigurationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StylingConfigurationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table]
#[JMSA\ExclusionPolicy('all')]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class StylingConfiguration implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'stylingConfigurations')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  #[ORM\Column(nullable: true)]
  private ?array $stylings = null;

  #[ORM\Column(name: 'name', length: 255)]
  #[Assert\Length(min: 1, max: 255)]
  #[JMSA\Expose]
  private string $name = '';

  /** @var Collection<int, StylingConfigurationConceptOverride> */
  #[ORM\OneToMany(mappedBy: 'stylingConfiguration', targetEntity: StylingConfigurationConceptOverride::class, cascade: ['remove'], fetch: 'EXTRA_LAZY')]
  private Collection $conceptOverrides;

  /** @var Collection<int, StylingConfigurationRelationOverride> */
  #[ORM\OneToMany(mappedBy: 'stylingConfiguration', targetEntity: StylingConfigurationRelationOverride::class, cascade: ['remove'], fetch: 'EXTRA_LAZY')]
  private Collection $relationOverrides;

  #[Override]
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
