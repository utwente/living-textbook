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
use Drenso\Shared\Helper\StringHelper;
use Drenso\Shared\Interfaces\IdInterface;
use JMS\Serializer\Annotation as Serializer;
use Override;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class RelationType.
 *
 * @author BobV
 *
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="App\Repository\RelationTypeRepository")
 *
 * We do not enable the soft-deletable extension here, as soft-deleted relations should still work after they have been
 * removed. They should however no longer be displayed in the list/edit possibilities.
 * //Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class RelationType implements StudyAreaFilteredInterface, ReviewableInterface, IdInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

  /**
   * @ORM\ManyToOne(targetEntity="StudyArea", inversedBy="relationTypes")
   *
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private ?StudyArea $studyArea = null;

  /**
   * @ORM\Column(name="name", type="string", length=100, nullable=false)
   *
   * @Assert\NotBlank()
   *
   * @Assert\Length(min=3, max=100)
   *
   * @Serializer\Groups({"Default", "review_change", "name_only"})
   *
   * @Serializer\Type("string")
   */
  private string $name = '';

  /**
   * @ORM\Column(name="description", type="text", nullable=true)
   *
   * @Serializer\Groups({"Default", "review_change"})
   *
   * @Serializer\Type("string")
   */
  private ?string $description = null;

  /**
   * @throws IncompatibleChangeException
   * @throws IncompatibleFieldChangedException
   */
  #[Override]
  public function applyChanges(PendingChange $change, EntityManagerInterface $em, bool $ignoreEm = false): void
  {
    $changeObj = $this->testChange($change);
    assert($changeObj instanceof self);

    foreach ($change->getChangedFields() as $changedField) {
      match ($changedField) {
        'name'        => $this->setName($changeObj->getName()),
        'description' => $this->setDescription($changeObj->getDescription()),
        default       => throw new IncompatibleFieldChangedException($this, $changedField),
      };
    }
  }

  #[Override]
  public function getReviewTitle(): string
  {
    return $this->getName();
  }

  /** Get camelized name, for usage in RDF export. */
  public function getCamelizedName(): string
  {
    return lcfirst(str_replace(' ', '', ucwords($this->getName())));
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): RelationType
  {
    $this->description = StringHelper::emptyToNull($description);

    return $this;
  }

  public function getName(): string
  {
    return $this->name;
  }

  public function setName(string $name): RelationType
  {
    $this->name = trim($name);

    return $this;
  }

  #[Override]
  public function getStudyArea(): ?StudyArea
  {
    return $this->studyArea;
  }

  #[Override]
  public function setStudyArea(?StudyArea $studyArea): RelationType
  {
    $this->studyArea = $studyArea;

    return $this;
  }
}
