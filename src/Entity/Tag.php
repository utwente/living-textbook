<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

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
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 *
 *  @ApiResource(
 *     attributes={},
 *     collectionOperations={"get", "post"},
 *     itemOperations={"get", "put", "delete"},
 *     normalizationContext={"groups"={"tag:read"}},
 *     denormalizationContext={"groups"={"tag:write"}},
 * )
 * @ApiFilter(SearchFilter::class, properties={"studyArea": "exact"})
 *
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class Tag implements StudyAreaFilteredInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var StudyArea|null
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="tags")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @var Concept[]|Collection
   *
   * @ORM\ManyToMany(targetEntity="App\Entity\Concept", mappedBy="tags")
   */
  private $concepts;

  /**
   * @var string
   *
   * @ORM\Column(length=25, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(max=25)
   *
   * @JMSA\Expose()
   *
   * @Groups({"studyarea:read", "studyarea:write", "tag:read", "tag:write"})
   *
   */
  private $name;

  /**
   * @var string
   *
   * @ORM\Column(length=10, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(max=10)
   *
   * @JMSA\Expose()
   *
   * @Groups({"studyarea:read", "studyarea:write", "tag:read", "tag:write"})
   *
   */
  private $color;

  public function __construct()
  {
    $this->name  = '';
    $this->color = '#8FBDAF';

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
   * @return StudyArea|null
   */
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return self
   */
  public function setStudyArea(StudyArea $studyArea): self
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
   * @return Tag
   */
  public function setName(string $name): self
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getColor(): string
  {
    return $this->color;
  }

  /**
   * @param string $color
   *
   * @return Tag
   */
  public function setColor(string $color): self
  {
    $this->color = $color;

    return $this;
  }

  /**
   * @return int|null
   *
   * @Groups({"tag:read"})
   */
  public function getId(): ?int
  {
    return $this->id;
  }
}
