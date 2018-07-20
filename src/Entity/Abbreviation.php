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
 * Class Abbreviation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\AbbreviationRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class Abbreviation
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var StudyArea|null
   *
   * @ORM\ManyToOne(targetEntity="StudyArea")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @var string
   *
   * @ORM\Column(name="abbreviation", length=25, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=25)
   *
   * @JMSA\Expose()
   */
  private $abbreviation;

  /**
   * @var string
   * @ORM\Column(name="meaning", length=255, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=255)
   *
   * @JMSA\Expose()
   */
  private $meaning;

  /**
   * Abbreviation constructor.
   */
  public function __construct()
  {
    $this->abbreviation = '';
    $this->meaning      = '';
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
   * @return Abbreviation
   */
  public function setStudyArea(StudyArea $studyArea): Abbreviation
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  /**
   * @return string
   */
  public function getAbbreviation(): string
  {
    return $this->abbreviation;
  }

  /**
   * @param string $abbreviation
   *
   * @return Abbreviation
   */
  public function setAbbreviation(string $abbreviation): Abbreviation
  {
    $this->abbreviation = $abbreviation;

    return $this;
  }

  /**
   * @return string
   */
  public function getMeaning(): string
  {
    return $this->meaning;
  }

  /**
   * @param string $meaning
   *
   * @return Abbreviation
   */
  public function setMeaning(string $meaning): Abbreviation
  {
    $this->meaning = $meaning;

    return $this;
  }
}
