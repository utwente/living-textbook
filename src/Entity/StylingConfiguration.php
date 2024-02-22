<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
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
