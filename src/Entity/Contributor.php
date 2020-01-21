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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Contributor
 *
 * @ORM\Entity(repositoryClass="App\Repository\ContributorRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Contributor implements StudyAreaFilteredInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var Concept[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="contributors")
   */
  private $concepts;

  /**
   * @var StudyArea|null
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="contributors")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @var string
   * @ORM\Column(name="name", type="string", length=512, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=512)
   */
  private $name;

  /**
   * @var string|null
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   *
   * @Assert\Length(max=1024)
   */
  private $description;

  /**
   * @var string|null
   *
   * @ORM\Column(name="url", type="string", length=512, nullable=true)
   *
   * @Assert\Url()
   * @Assert\Length(max=512)
   */
  private $url;

  /**
   * @var bool
   *
   * @ORM\Column(name="broken", type="boolean", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $broken;

  /**
   * Contributor constructor.
   */
  public function __construct()
  {
    $this->name   = '';
    $this->broken = false;

    $this->concepts = new ArrayCollection();
  }

  /**
   * @return Concept[]|Collection
   */
  public function getConcepts()
  {
    return $this->concepts;
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
   * @return Contributor
   */
  public function setName(string $name): self
  {
    $this->name = $name;

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
   * @return Contributor
   */
  public function setDescription(?string $description): Contributor
  {
    $this->description = $description;

    return $this;
  }

  /**
   * @return string|null
   */
  public function getUrl(): ?string
  {
    return $this->url;
  }

  /**
   * @param string|null $url
   *
   * @return Contributor
   */
  public function setUrl(?string $url): Contributor
  {
    $this->url = $url;

    return $this;
  }


  /**
   * @return bool
   */
  public function isBroken(): bool
  {
    return $this->broken;
  }

  /**
   * @param bool $broken
   *
   * @return Contributor
   */
  public function setBroken(bool $broken): Contributor
  {
    $this->broken = $broken;

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
   * @return Contributor
   */
  public function setStudyArea(StudyArea $studyArea): Contributor
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
