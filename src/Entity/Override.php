<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 *
 * @Gedmo\SoftDeleteable()
 *
 * @JMSA\ExclusionPolicy("all")
 */
abstract class Override implements StudyAreaFilteredInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @ORM\ManyToOne(targetEntity="StudyArea")
   *
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private StudyArea $studyArea;

  /**
   * @ORM\Column(type="json", nullable=true)
   *
   * @JMSA\Expose()
   *
   * @Assert\NotNull()
   */
  private ?array $override = null;

  public function __construct(
    StudyArea $studyArea,
    ?array $override
  ) {
    $this->studyArea = $studyArea;
    $this->override  = $override;
  }

  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
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
