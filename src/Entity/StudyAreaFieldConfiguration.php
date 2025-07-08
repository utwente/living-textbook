<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Repository\StudyAreaFieldConfigurationRepository;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: StudyAreaFieldConfigurationRepository::class)]
#[ORM\Table]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class StudyAreaFieldConfiguration implements IdInterface
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptDefinitionName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptIntroductionName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptSynonymsName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptPriorKnowledgeName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptTheoryExplanationName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptHowtoName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptExamplesName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $conceptSelfAssessmentName = null;

  #[Assert\Length(max: 50)]
  #[ORM\Column(length: 50, nullable: true)]
  private ?string $learningOutcomeObjName = null;

  public function getConceptDefinitionName(): ?string
  {
    return $this->conceptDefinitionName;
  }

  public function setConceptDefinitionName(?string $conceptDefinitionName): self
  {
    $this->conceptDefinitionName = $conceptDefinitionName;

    return $this;
  }

  public function getConceptIntroductionName(): ?string
  {
    return $this->conceptIntroductionName;
  }

  public function setConceptIntroductionName(?string $conceptIntroductionName): self
  {
    $this->conceptIntroductionName = $conceptIntroductionName;

    return $this;
  }

  public function getConceptSynonymsName(): ?string
  {
    return $this->conceptSynonymsName;
  }

  public function setConceptSynonymsName(?string $conceptSynonymsName): self
  {
    $this->conceptSynonymsName = $conceptSynonymsName;

    return $this;
  }

  public function getConceptPriorKnowledgeName(): ?string
  {
    return $this->conceptPriorKnowledgeName;
  }

  public function setConceptPriorKnowledgeName(?string $conceptPriorKnowledgeName): self
  {
    $this->conceptPriorKnowledgeName = $conceptPriorKnowledgeName;

    return $this;
  }

  public function getConceptTheoryExplanationName(): ?string
  {
    return $this->conceptTheoryExplanationName;
  }

  public function setConceptTheoryExplanationName(?string $conceptTheoryExplanationName): self
  {
    $this->conceptTheoryExplanationName = $conceptTheoryExplanationName;

    return $this;
  }

  public function getConceptHowtoName(): ?string
  {
    return $this->conceptHowtoName;
  }

  public function setConceptHowtoName(?string $conceptHowtoName): self
  {
    $this->conceptHowtoName = $conceptHowtoName;

    return $this;
  }

  public function getConceptExamplesName(): ?string
  {
    return $this->conceptExamplesName;
  }

  public function setConceptExamplesName(?string $conceptExamplesName): self
  {
    $this->conceptExamplesName = $conceptExamplesName;

    return $this;
  }

  public function getConceptSelfAssessmentName(): ?string
  {
    return $this->conceptSelfAssessmentName;
  }

  public function setConceptSelfAssessmentName(?string $conceptSelfAssessmentName): self
  {
    $this->conceptSelfAssessmentName = $conceptSelfAssessmentName;

    return $this;
  }

  public function getLearningOutcomeObjName(): ?string
  {
    return $this->learningOutcomeObjName;
  }

  public function setLearningOutcomeObjName(?string $learningOutcomeObjName): self
  {
    $this->learningOutcomeObjName = $learningOutcomeObjName;

    return $this;
  }
}
