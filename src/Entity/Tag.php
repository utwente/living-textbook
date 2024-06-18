<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Repository\TagRepository;
use App\Validator\Constraint\Color;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 *
 * @JMSA\ExclusionPolicy("all")
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\Table]
class Tag implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  #[Assert\NotNull]
  #[ORM\ManyToOne(inversedBy: 'tags')]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  /** @var Collection<Concept> */
  #[ORM\ManyToMany(targetEntity: Concept::class, mappedBy: 'tags')]
  private Collection $concepts;

  /** @JMSA\Expose() */
  #[Assert\NotBlank]
  #[Assert\Length(max: 25)]
  #[ORM\Column(length: 25, nullable: false)]
  private string $name = '';

  /**
   * @Color()
   *
   * @JMSA\Expose()
   */
  #[Assert\NotBlank]
  #[ORM\Column(length: 10, nullable: false)]
  private string $color = '#8FBDAF';

  /** @JMSA\Expose() */
  #[Assert\Length(max: 1024)]
  #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
  private ?string $description = null;

  public function __construct()
  {
    $this->concepts = new ArrayCollection();
  }

  /** @return Collection<Concept> */
  public function getConcepts(): Collection
  {
    return $this->concepts;
  }

  #[Override]
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  public function getColor(): string
  {
    return $this->color;
  }

  public function setColor(string $color): self
  {
    $this->color = $color;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): self
  {
    $this->description = $description;

    return $this;
  }
}
