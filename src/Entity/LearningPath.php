<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LearningPath
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\LearningPathRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class LearningPath
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var StudyArea|null
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="learningPaths")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * Learning path name
   *
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(max=255)
   */
  private $name = '';

  /**
   * Learning path question
   *
   * @var string
   *
   * @ORM\Column(name="question", type="string", length=1024, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(max=1024)
   */
  private $question = '';

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
   * @return LearningPath
   */
  public function setStudyArea(StudyArea $studyArea): LearningPath
  {
    $this->studyArea = $studyArea;

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
   * @return LearningPath
   */
  public function setName(string $name): LearningPath
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getQuestion(): string
  {
    return $this->question;
  }

  /**
   * @param string $question
   *
   * @return LearningPath
   */
  public function setQuestion(string $question): LearningPath
  {
    $this->question = $question;

    return $this;
  }

}
