<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Validator\Constraint\Data\WordCount;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LearningOutcome
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\LearningOutcomeRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @UniqueEntity(fields={"studyArea","number"},errorPath="number",message="learning-outcome.number-already-used")
 */
class LearningOutcome
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var Concept[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="learningOutcomes")
   */
  private $concepts;

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
   * Learning outcome number
   *
   * @var int
   *
   * @ORM\Column(name="number", type="integer", nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Range(min="0", max="1000")
   */
  private $number;

  /**
   * Learning outcome name
   *
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min="0", max="255")
   */
  private $name;

  /**
   * Learning outcome text
   *
   * @var string
   *
   * @ORM\Column(name="text", type="text", nullable=false)
   *
   * @Assert\NotBlank()
   * @WordCount()
   */
  private $text;

  public function __construct()
  {
    $this->number = 0;
    $this->name   = '';
    $this->text   = '';

    $this->concepts = new ArrayCollection();
  }

  public function getShortName()
  {
    return sprintf('%d - %s', $this->number, $this->name);
  }

  /**
   * @return int
   */
  public function getNumber(): int
  {
    return $this->number;
  }

  /**
   * @param int $number
   *
   * @return LearningOutcome
   */
  public function setNumber(int $number): LearningOutcome
  {
    $this->number = $number;

    return $this;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
  }

  /**
   * @param string $name
   *
   * @return LearningOutcome
   */
  public function setName(string $name): LearningOutcome
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getText(): string
  {
    return $this->text;
  }

  /**
   * @param string $text
   *
   * @return LearningOutcome
   */
  public function setText(string $text): LearningOutcome
  {
    $this->text = $text;

    return $this;
  }

  /**
   * @return Concept[]|Collection
   */
  public function getConcepts()
  {
    return $this->concepts;
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
   * @return LearningOutcome
   */
  public function setStudyArea(StudyArea $studyArea): LearningOutcome
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
