<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Validator\Constraint\Color;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class Tag implements StudyAreaFilteredInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="tags")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   * @Assert\NotNull()
   */
  private ?StudyArea $studyArea = null;

  /**
   * @var Collection<Concept>
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="tags")
   */
  private Collection $concepts;

  /**
   *
   * @ORM\Column(length=25, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(max=25)
   * @JMSA\Expose()
   */
  private string $name = '';

  /**
   *
   * @ORM\Column(length=10, nullable=false)
   *
   * @Assert\NotBlank()
   * @Color()
   * @JMSA\Expose()
   */
  private string $color = '#8FBDAF';

  /**
   * @var string
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   *
   * @Assert\Length(max=1024)
   *
   * @JMSA\Expose()
   */
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
