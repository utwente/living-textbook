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
class LayoutConfiguration implements StudyAreaFilteredInterface
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  #[ORM\ManyToOne(inversedBy: 'layoutConfigurations')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  #[Assert\NotNull]
  private ?StudyArea $studyArea = null;

  /** @var mixed[]|null */
  #[ORM\Column(nullable: true)]
  private ?array $layouts = null;

  #[ORM\Column(name: 'name', length: 255)]
  #[Assert\NotBlank]
  #[Assert\Length(min: 1, max: 255)]
  #[JMSA\Expose]
  private string $name = '';

  /** @var Collection<int, LayoutConfigurationOverride> */
  #[ORM\OneToMany(targetEntity: LayoutConfigurationOverride::class, mappedBy: 'layoutConfiguration', cascade: ['remove'], fetch: 'EXTRA_LAZY')]
  /** @phpstan-ignore-next-line property.onlyRead */
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

  /** @return mixed[]|null */
  public function getLayouts(): ?array
  {
    return $this->layouts;
  }

  /** @param mixed[]|null $layouts */
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
