<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Repository\StudyAreaFieldConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Helper\StringHelper;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudyAreaFieldConfigurationRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class StudyAreaFieldConfiguration implements IdInterface
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptDefinitionName = null {
    set => $this->conceptDefinitionName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptIntroductionName = null {
    set => $this->conceptIntroductionName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptSynonymsName = null {
    set => $this->conceptSynonymsName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptPriorKnowledgeName = null {
    set => $this->conceptPriorKnowledgeName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptTheoryExplanationName = null {
    set => $this->conceptTheoryExplanationName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptHowtoName = null {
    set => $this->conceptHowtoName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptExamplesName = null {
    set => $this->conceptExamplesName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $conceptSelfAssessmentName = null {
    set => $this->conceptSelfAssessmentName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $learningOutcomeObjName = null {
    set => $this->learningOutcomeObjName = StringHelper::emptyToNull($value);
  }

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  public ?string $skillObjName = null {
    set => $this->skillObjName = StringHelper::emptyToNull($value);
  }
}
