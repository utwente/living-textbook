<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Contracts\ReviewableInterface;
use App\Entity\Contracts\StudyAreaFilteredInterface;
use App\Entity\Traits\ReviewableTrait;
use App\Review\Exception\IncompatibleChangeException;
use App\Review\Exception\IncompatibleFieldChangedException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RelationType
 *
 * @author BobV
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\RelationTypeRepository")
 *
 * We do not enable the soft-deletable extension here, as soft-deleted relation should still work after they have been
 * removed. The should however no longer be displayed in the list/edit possibilities.
 * //Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class RelationType implements StudyAreaFilteredInterface, ReviewableInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

  /**
   * @var StudyArea|null
   *
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="relationTypes")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=100, nullable=false)
   *
   * @Assert\NotBlank()
   * @Assert\Length(min=3, max=100)
   *
   * @Serializer\Groups({"Default", "review_change", "name_only"})
   * @Serializer\Type("string")
   */
  private $name;

  /**
   * @var string|null
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   *
   * @Serializer\Groups({"Default", "review_change"})
   * @Serializer\Type("string")
   */
  private $description;

  /**
   * RelationType constructor.
   */
  public function __construct()
  {
    $this->name = '';
  }

  /**
   * @param PendingChange          $change
   * @param EntityManagerInterface $em
   *
   * @throws IncompatibleChangeException
   * @throws IncompatibleFieldChangedException
   */
  public function applyChanges(PendingChange $change, EntityManagerInterface $em): void
  {
    $changeObj = $this->testChange($change);
    assert($changeObj instanceof self);

    foreach ($change->getChangedFields() as $changedField) {
      switch ($changedField) {
        case 'name':
          $this->setName($changeObj->getName());
          break;
        case 'description':
          $this->setDescription($changeObj->getDescription());
          break;
        default:
          throw new IncompatibleFieldChangedException($this, $changedField);
      }
    }
  }

  public function getReviewTitle(): string
  {
    return $this->getName();
  }

  /**
   * Get camelized name, for usage in RDF export
   *
   * @return string
   */
  public function getCamelizedName(): string
  {
    return lcfirst(str_replace(' ', '', ucwords($this->getName())));
  }

  /**
   * @return null|string
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * @param null|string $description
   *
   * @return RelationType
   */
  public function setDescription(?string $description): RelationType
  {
    $this->description = trim($description);

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
   * @return RelationType
   */
  public function setName(string $name): RelationType
  {
    $this->name = trim($name);

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
   * @param StudyArea|null $studyArea
   *
   * @return RelationType
   */
  public function setStudyArea(?StudyArea $studyArea): RelationType
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
