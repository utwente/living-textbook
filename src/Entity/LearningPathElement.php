<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class LearningPathConcept.
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="App\Repository\LearningPathElementRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMSA\ExclusionPolicy("all")
 */
class LearningPathElement implements IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * Belongs to a certain learning path.
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\LearningPath", inversedBy="elements")
   *
   * @ORM\JoinColumn(name="learning_path_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?LearningPath $learningPath = null;

  /**
   * Linked concept.
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\Concept")
   *
   * @ORM\JoinColumn(name="concept_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"Default", "review_change"})
   *
   * @JMSA\Type(Concept::class)
   *
   * @JMSA\MaxDepth(2)
   */
  private ?Concept $concept = null;

  /**
   * Transition to the next element, if any.
   *
   * @ORM\ManyToOne(targetEntity="LearningPathElement")
   *
   * @ORM\JoinColumn(name="next_id", referencedColumnName="id", nullable=true)
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"review_change"})
   *
   * @JMSA\Type(LearningPathElement::class)
   *
   * @JMSA\MaxDepth(2)
   */
  private ?LearningPathElement $next = null;

  /**
   * Optional description of the transition to the next element.
   *
   * @ORM\Column(type="string", length=1024, nullable=true)
   *
   * @Assert\Length(max=1024)
   *
   * @JMSA\Expose()
   *
   * @JMSA\Groups({"Default","review_change"})
   *
   * @JMSA\Type("string")
   */
  private ?string $description = null;

  public function getLearningPath(): ?LearningPath
  {
    return $this->learningPath;
  }

  public function setLearningPath(?LearningPath $learningPath): LearningPathElement
  {
    $this->learningPath = $learningPath;

    return $this;
  }

  public function getConcept(): ?Concept
  {
    return $this->concept;
  }

  public function setConcept(?Concept $concept): LearningPathElement
  {
    $this->concept = $concept;

    return $this;
  }

  public function getNext(): ?LearningPathElement
  {
    return $this->next;
  }

  /**
   * @JMSA\Expose()
   *
   * @JMSA\VirtualProperty()
   *
   * @JMSA\SerializedName("next")
   *
   * @JMSA\Groups({"Default"})
   */
  public function getNextId(): ?int
  {
    return $this->next ? $this->next->getId() : null;
  }

  public function setNext(?LearningPathElement $next): LearningPathElement
  {
    $this->next = $next;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): LearningPathElement
  {
    $this->description = $description;

    return $this;
  }
}
