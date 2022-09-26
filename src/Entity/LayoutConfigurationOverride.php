<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\LayoutConfigurationOverrideRepository",)
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity({"concept", "layoutConfiguration"})
 * @Gedmo\SoftDeleteable()
 * @JMSA\ExclusionPolicy("all")
 */
class LayoutConfigurationOverride implements StudyAreaFilteredInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @ORM\ManyToOne(targetEntity="StudyArea")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private StudyArea $studyArea;

  /**
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="layoutOverrides")
   *
   * @JMSA\Expose()
   */
  private Concept $concept;

  /** @ORM\ManyToOne(targetEntity="LayoutConfiguration", inversedBy="overrides") */
  private LayoutConfiguration $layoutConfiguration;

  /**
   * @ORM\Column(type="json", nullable=true)
   *
   * @JMSA\Expose()
   * @Assert\NotNull()
   */
  private ?array $override = null;

  public function __construct(
      StudyArea $studyArea,
      Concept $concept,
      LayoutConfiguration $layoutConfiguration,
      ?array $override
  ) {
    $this->studyArea           = $studyArea;
    $this->concept             = $concept;
    $this->layoutConfiguration = $layoutConfiguration;
    $this->override            = $override;
  }

  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  public function getConcept(): Concept
  {
    return $this->concept;
  }

  public function getLayoutConfiguration(): LayoutConfiguration
  {
    return $this->layoutConfiguration;
  }

  public function getOverride(): ?array
  {
    return $this->override;
  }

  public function setOverride(?array $override): self
  {
    $this->override = $override;

    return $this;
  }
}
