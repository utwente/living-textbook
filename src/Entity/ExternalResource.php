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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ExternalResource
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ExternalResourceRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class ExternalResource
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var Concept[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="externalResources")
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
   * @var string
   * @ORM\Column(name="title", type="text", length=255, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=1, max=255)
   */
  private $title;

  /**
   * @var string
   *
   * @ORM\Column(name="description", type="text", length=1000, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(max=1000)
   * @WordCount(min=1)
   */
  private $description;

  /**
   * @var string|null
   *
   * @ORM\Column(name="url", type="text", length=1000, nullable=true)
   *
   * @Assert\Url()
   * @Assert\Length(max=1000)
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
   * ExternalResource constructor.
   */
  public function __construct()
  {
    $this->title       = '';
    $this->description = '';
    $this->url         = '';
    $this->broken      = false;

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
  public function getTitle(): string
  {
    return $this->title;
  }

  /**
   * @param string $title
   *
   * @return ExternalResource
   */
  public function setTitle(string $title): ExternalResource
  {
    $this->title = $title;

    return $this;
  }

  /**
   * @return string
   */
  public function getDescription(): string
  {
    return $this->description;
  }

  /**
   * @param string $description
   *
   * @return ExternalResource
   */
  public function setDescription(string $description): ExternalResource
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
   * @return ExternalResource
   */
  public function setUrl(?string $url): ExternalResource
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
   * @return ExternalResource
   */
  public function setBroken(bool $broken): ExternalResource
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
   * @return ExternalResource
   */
  public function setStudyArea(StudyArea $studyArea): ExternalResource
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
