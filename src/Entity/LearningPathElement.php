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
 * Class LearningPathConcept
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\LearningPathElementRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class LearningPathElement
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * Belongs to a certain learning path
   *
   * @var LearningPath|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\LearningPath", inversedBy="elements")
   * @ORM\JoinColumn(name="learning_path_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $learningPath;

  /**
   * Linked concept
   *
   * @var Concept|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\Concept")
   * @ORM\JoinColumn(name="concept_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"Default", "review_change"})
   * @JMSA\Type(Concept::class)
   * @JMSA\MaxDepth(2)
   */
  private $concept;

  /**
   * Transition to the next element, if any
   *
   * @var LearningPathElement|null
   *
   * @ORM\ManyToOne(targetEntity="LearningPathElement")
   * @ORM\JoinColumn(name="next_id", referencedColumnName="id", nullable=true)
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"review_change"})
   * @JMSA\Type(LearningPathElement::class)
   * @JMSA\MaxDepth(2)
   */
  private $next;

  /**
   * Optional description of the transition to the next element
   *
   * @var string|null
   *
   * @ORM\Column(type="string", length=1024, nullable=true)
   * @Assert\Length(max=1024)
   *
   * @JMSA\Expose()
   * @JMSA\Groups({"Default","review_change"})
   * @JMSA\Type("string")
   */
  private $description;

  /**
   * @return LearningPath|null
   */
  public function getLearningPath(): ?LearningPath
  {
    return $this->learningPath;
  }

  /**
   * @param LearningPath|null $learningPath
   *
   * @return LearningPathElement
   */
  public function setLearningPath(?LearningPath $learningPath): LearningPathElement
  {
    $this->learningPath = $learningPath;

    return $this;
  }

  /**
   * @return Concept|null
   */
  public function getConcept(): ?Concept
  {
    return $this->concept;
  }

  /**
   * @param Concept|null $concept
   *
   * @return LearningPathElement
   */
  public function setConcept(?Concept $concept): LearningPathElement
  {
    $this->concept = $concept;

    return $this;
  }

  /**
   * @return LearningPathElement|null
   */
  public function getNext(): ?LearningPathElement
  {
    return $this->next;
  }

  /**
   * @return int|null
   *
   * @JMSA\Expose()
   * @JMSA\VirtualProperty()
   * @JMSA\SerializedName("next")
   * @JMSA\Groups({"Default"})
   */
  public function getNextId(): ?int
  {
    return $this->next ? $this->next->getId() : NULL;
  }

  /**
   * @param LearningPathElement|null $next
   *
   * @return LearningPathElement
   */
  public function setNext(?LearningPathElement $next): LearningPathElement
  {
    $this->next = $next;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * @param string|null $description
   *
   * @return LearningPathElement
   */
  public function setDescription(?string $description): LearningPathElement
  {
    $this->description = $description;

    return $this;
  }

}
