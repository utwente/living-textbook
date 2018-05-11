<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMSA;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class StudyArea
 *
 * @author Tobias
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\StudyAreaRepository")
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 * @JMSA\ExclusionPolicy("all")
 */
class StudyArea
{

  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * @var string
   *
   * @ORM\Column(name="name", type="string", length=255, nullable=false)
   * @Assert\Length(min=3, max=255)
   *
   * @JMSA\Expose()
   */
  private $name;

  /**
   * @var Collection|Concept[]
   *
   * @ORM\OneToMany(targetEntity="Concept", mappedBy="studyArea", cascade={"persist","remove"})
   *
   * @JMSA\Expose()
   */
  private $concepts;

  /**
   * @var User
   *
   * @ORM\ManyToOne(targetEntity="User")
   * @ORM\JoinColumn(name="owner_user_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $owner;

  /**
   * StudyArea constructor.
   */
  public function __construct()
  {
    $this->name     = '';
    $this->concepts = new ArrayCollection();
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
   * @return StudyArea
   */
  public function setName(string $name): StudyArea
  {
    $this->name = $name;

    return $this;
  }

  /**
   * @return Concept[]|Collection
   */
  public function getConcepts()
  {
    return $this->concepts;
  }

  /**
   * @param Concept $concept
   *
   * @return StudyArea
   */
  public function addConcept(Concept $concept): StudyArea
  {
    // Check whether the studyarea is set, otherwise set it as this
    if (!$concept->getStudyArea()) {
      $concept->setStudyArea($this);
    }
    $this->concepts->add($concept);

    return $this;
  }

  /**
   * @param Concept $concept
   *
   * @return StudyArea
   */
  public function removeConcept(Concept $concept): StudyArea
  {
    $this->concepts->removeElement($concept);

    return $this;
  }

  /**
   * @return string
   */
  public function __toString(): string
  {
    return $this->getName();
  }

  /**
   * @return User|null
   */
  public function getOwner(): ?User
  {
    return $this->owner;
  }

  /**
   * @param User $owner
   *
   * @return StudyArea
   */
  public function setOwner(User $owner): StudyArea
  {
    $this->owner = $owner;

    return $this;
  }

}
