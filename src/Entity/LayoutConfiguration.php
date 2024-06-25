<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Repository\LayoutConfigurationRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LayoutConfigurationRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[Gedmo\SoftDeleteable]
#[JMSA\ExclusionPolicy('all')]
#[ORM\Table]
class LayoutConfiguration implements StudyAreaFilteredInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  #[ORM\ManyToOne(targetEntity: \StudyArea::class, inversedBy: 'layoutConfigurations')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  #[Assert\NotNull]
  private ?StudyArea $studyArea = null;

  #[ORM\Column(type: 'json', nullable: true)]
  private ?array $layouts = null;

  #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: false)]
  #[Assert\NotBlank]
  #[Assert\Length(min: 1, max: 255)]
  #[JMSA\Expose]
  private string $name = '';

  /** @var Collection<int, LayoutConfigurationOverride> */
  #[ORM\OneToMany(targetEntity: LayoutConfigurationOverride::class, mappedBy: 'layoutConfiguration', fetch: 'EXTRA_LAZY', cascade: ['remove'])]
  private Collection $overrides;

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

  public function getLayouts(): ?array
  {
    return $this->layouts;
  }

  public function setLayouts(?array $layouts): self
  {
    $this->layouts = $layouts;

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

  /** @return Collection<int, LayoutConfigurationOverride> */
  public function getOverrides(): Collection
  {
    return $this->overrides;
  }
}
