<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StudyArea
 *
 * @author Tobias
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ConceptStudyAreaRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class ConceptStudyArea
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var Concept
   *
   * @ORM\ManyToOne(targetEntity="Concept", inversedBy="studyAreas")
   * @ORM\JoinColumn(name="concept_id", referencedColumnName="id")
   *
   * @Assert\NotNull()
   */
  private $concept;

  /**
   * @var StudyArea
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="concepts")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id")
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @return Concept|null
   */
  public function getConcept(): ?Concept
  {
    return $this->concept;
  }

  /**
   * @param Concept $concept
   *
   * @return ConceptStudyArea
   */
  public function setConcept(Concept $concept): ConceptStudyArea
  {
    $this->concept = $concept;

    return $this;
  }

  /**
   * @return StudyArea|null
   */
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return ConceptStudyArea|null
   */
  public function setStudyArea(StudyArea $studyArea): ConceptStudyArea
  {
    $this->studyArea = $studyArea;

    return $this;
  }

}
