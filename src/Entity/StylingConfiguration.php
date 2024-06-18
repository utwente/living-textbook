<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Repository\StylingConfigurationRepository;
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
}
