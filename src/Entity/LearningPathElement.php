<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Repository\LearningPathElementRepository;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
#[ORM\Entity(repositoryClass: LearningPathElementRepository::class)]
#[ORM\Table]
#[JMSA\ExclusionPolicy('all')]
class LearningPathElement implements IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /** Belongs to a certain learning path. */
  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'elements')]
  #[ORM\JoinColumn(name: 'learning_path_id', referencedColumnName: 'id', nullable: false)]
  private ?LearningPath $learningPath = null;

  /** Linked concept. */
  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(name: 'concept_id', referencedColumnName: 'id', nullable: false)]
  #[JMSA\Expose]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type(Concept::class)]
  #[JMSA\MaxDepth(2)]
  private ?Concept $concept = null;

  /** Transition to the next element, if any. */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(name: 'next_id', referencedColumnName: 'id', nullable: true)]
  #[JMSA\Expose]
  #[JMSA\Groups(['review_change'])]
  #[JMSA\Type(LearningPathElement::class)]
  #[JMSA\MaxDepth(2)]
  private ?LearningPathElement $next = null;

  /** Optional description of the transition to the next element. */
  #[Assert\Length(max: 1024)]
  #[ORM\Column(length: 1024, nullable: true)]
  #[JMSA\Expose]
  #[JMSA\Groups(['Default', 'review_change'])]
  #[JMSA\Type('string')]
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

  #[JMSA\Expose]
  #[JMSA\VirtualProperty]
  #[JMSA\SerializedName('next')]
  #[JMSA\Groups(['Default'])]
  public function getNextId(): ?int
  {
    return $this->next?->getId();
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
