<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * This entity is
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\StudyAreaFieldConfigurationRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class StudyAreaFieldConfiguration
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptDefinitionName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptIntroductionName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptSynonymsName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptPriorKnowledgeName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptTheoryExplanationName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptHowtoName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptExamplesName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $conceptSelfAssessmentName;

  /**
   * @var string|null
   * @ORM\Column(type="string", length=50, nullable=true)
   * @Assert\Length(max="50")
   */
  private $learningOutcomeObjName;

  /**
   * @return string|null
   */
  public function getConceptDefinitionName(): ?string
  {
    return $this->conceptDefinitionName;
  }

  /**
   * @param string|null $conceptDefinitionName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptDefinitionName(?string $conceptDefinitionName): self
  {
    $this->conceptDefinitionName = $conceptDefinitionName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getConceptIntroductionName(): ?string
  {
    return $this->conceptIntroductionName;
  }

  /**
   * @param string|null $conceptIntroductionName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptIntroductionName(?string $conceptIntroductionName): self
  {
    $this->conceptIntroductionName = $conceptIntroductionName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getConceptSynonymsName(): ?string
  {
    return $this->conceptSynonymsName;
  }

  /**
   * @param string|null $conceptSynonymsName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptSynonymsName(?string $conceptSynonymsName): self
  {
    $this->conceptSynonymsName = $conceptSynonymsName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getConceptPriorKnowledgeName(): ?string
  {
    return $this->conceptPriorKnowledgeName;
  }

  /**
   * @param string|null $conceptPriorKnowledgeName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptPriorKnowledgeName(?string $conceptPriorKnowledgeName): self
  {
    $this->conceptPriorKnowledgeName = $conceptPriorKnowledgeName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getConceptTheoryExplanationName(): ?string
  {
    return $this->conceptTheoryExplanationName;
  }

  /**
   * @param string|null $conceptTheoryExplanationName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptTheoryExplanationName(?string $conceptTheoryExplanationName): self
  {
    $this->conceptTheoryExplanationName = $conceptTheoryExplanationName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getConceptHowtoName(): ?string
  {
    return $this->conceptHowtoName;
  }

  /**
   * @param string|null $conceptHowtoName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptHowtoName(?string $conceptHowtoName): self
  {
    $this->conceptHowtoName = $conceptHowtoName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getConceptExamplesName(): ?string
  {
    return $this->conceptExamplesName;
  }

  /**
   * @param string|null $conceptExamplesName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptExamplesName(?string $conceptExamplesName): self
  {
    $this->conceptExamplesName = $conceptExamplesName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getConceptSelfAssessmentName(): ?string
  {
    return $this->conceptSelfAssessmentName;
  }

  /**
   * @param string|null $conceptSelfAssessmentName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setConceptSelfAssessmentName(?string $conceptSelfAssessmentName): self
  {
    $this->conceptSelfAssessmentName = $conceptSelfAssessmentName;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getLearningOutcomeObjName(): ?string
  {
    return $this->learningOutcomeObjName;
  }

  /**
   * @param string|null $learningOutcomeObjName
   *
   * @return StudyAreaFieldConfiguration
   */
  public function setLearningOutcomeObjName(?string $learningOutcomeObjName): self
  {
    $this->learningOutcomeObjName = $learningOutcomeObjName;

    return $this;
  }
}
