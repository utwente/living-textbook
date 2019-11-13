<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LearningPath
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\LearningPathRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class LearningPath implements StudyAreaFilteredInterface
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
   *
   * @JMSA\Expose()
   */
  private $name;

  /**
   * Learning path introduction
   *
   * @var string|null
   *
   * @ORM\Column(name="introduction", type="text", nullable=true)
   *
   * @Assert\NotBlank();
   */
  private $introduction;

  /**
   * Learning path question
   *
   * @var string
   *
   * @ORM\Column(name="question", type="string", length=1024, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(max=1024)
   *
   * @JMSA\Expose()
   */
  private $question;

  /**
   * @var Collection|LearningPathElement[]
   *
   * @ORM\OneToMany(targetEntity="App\Entity\LearningPathElement", mappedBy="learningPath",
   *   cascade={"persist", "remove"})
   *
   * @Assert\Valid()
   */
  private $elements;

  public function __construct()
  {
    $this->name     = '';
    $this->question = '';
    $this->elements = new ArrayCollection();
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

  /**
   * @return string|null
   */
  public function getIntroduction(): ?string
  {
    return $this->introduction;
  }

  /**
   * @param string|null $introduction
   *
   * @return LearningPath
   */
  public function setIntroduction(?string $introduction): LearningPath
  {
    $this->introduction = $introduction;

    return $this;
  }

  /**
   * Retrieve the ordered element list
   *
   * @return LearningPathElement[]|Collection
   */
  public function getElements()
  {
    return $this->elements;
  }

  /**
   * Get the elements ordered
   *
   * @return LearningPathElement[]|Collection
   *
   * @JMSA\Expose()
   * @JMSA\VirtualProperty()
   * @JMSA\SerializedName("elements")
   */
  public function getElementsOrdered()
  {
    return self::OrderElements($this->elements);
  }

  /**
   * Get the elements ordered
   *
   * @param Collection $elements
   *
   * @return LearningPathElement[]|Collection
   */
  public static function OrderElements(Collection $elements)
  {
    $result      = [];
    $mappingNext = [];
    foreach ($elements as $element) {
      if ($element->getNext()) {
        $mappingNext[$element->getNext()->getId()] = $element;
      } else {
        // No next, so is last element
        $result[] = $element;
      }
    }

    while (count($mappingNext) > 0) {
      $nextId   = end($result)->getId();
      $result[] = $mappingNext[$nextId];
      unset($mappingNext[$nextId]);
    }

    return new ArrayCollection(array_reverse($result));
  }

  /**
   * @param LearningPathElement $element
   *
   * @return LearningPath
   */
  public function addElement(LearningPathElement $element): LearningPath
  {
    if (!$element->getLearningPath()) {
      $element->setLearningPath($this);
    }

    $this->elements->add($element);

    return $this;
  }

  /**
   * @param LearningPathElement $element
   *
   * @return LearningPath
   */
  public function removeElement(LearningPathElement $element): LearningPath
  {
    $this->elements->removeElement($element);

    return $this;
  }

}
