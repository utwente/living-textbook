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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Contributor
 *
 * @ORM\Entity(repositoryClass="App\Repository\ContributorRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class Contributor implements StudyAreaFilteredInterface, ReviewableInterface
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;
  use ReviewableTrait;

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
   * @JMSA\Groups({"Default", "review_change"})
   * @JMSA\Type("string")
   */
  private $name;

  /**
   * @var string|null
   *
   * @ORM\Column(name="description", type="text", nullable=true)
   *
   * @Assert\Length(max=1024)
   * @JMSA\Groups({"Default", "review_change"})
   * @JMSA\Type("string")
   */
  private $description;

  /**
   * @var string|null
   *
   * @ORM\Column(name="url", type="string", length=512, nullable=true)
   *
   * @Assert\Url()
   * @Assert\Length(max=512)
   * @JMSA\Groups({"Default", "review_change"})
   * @JMSA\Type("string")
   */
  private $url;

  /**
   * @var string|null
   *
   * @ORM\Column(name="email", type="string", length=255, nullable=true)
   *
   * @Assert\Email()
   * @Assert\Length(max=255)
   * @JMSA\Groups({"Default", "review_change"})
   * @JMSA\Type("string")
   */
  private $email;

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
   * @param PendingChange          $change
   * @param EntityManagerInterface $em
   * @param bool                   $ignoreEm
   *
   * @throws IncompatibleChangeException
   * @throws IncompatibleFieldChangedException
   */
  public function applyChanges(PendingChange $change, EntityManagerInterface $em, bool $ignoreEm = false): void
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
        case 'url':
          $this->setUrl($changeObj->getUrl());
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
    $this->name = trim($name);

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
    $this->description = trim($description);

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
    $this->url = trim($url);

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
   * @return string|null
   */
  public function getEmail(): ?string
  {
    return $this->email;
  }

  /**
   * @param string|null $email
   *
   * @return Contributor
   */
  public function setEmail(?string $email): Contributor
  {
    $this->email = $email;

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
