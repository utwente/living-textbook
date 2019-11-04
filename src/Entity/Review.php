<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Review
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 */
class Review
{
  use IdTrait;
  use Blameable;

  /**
   * @var StudyArea
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\StudyArea")
   * @ORM\JoinColumn(name="study_area_id", referencedColumnName="id", nullable=false)
   *
   * @Assert\NotNull()
   */
  private $studyArea;

  /**
   * The pending changes in this review
   *
   * @var PendingChange[]|Collection
   *
   * @ORM\OneToMany(targetEntity="App\Entity\PendingChange", mappedBy="review", cascade={"remove"})
   *
   * @Assert\NotNull()
   * @Assert\Count(min=1)
   */
  private $pendingChanges;

  /**
   * The owner of the pending change (aka, the user who created it)
   *
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   *
   * @Assert\NotNull()
   */
  private $owner;

  /**
   * @var DateTime|null
   *
   * @ORM\Column(type="datetime")
   *
   * @Assert\Type("datetime")
   */
  private $requestedReviewAt;

  /**
   * The requested reviewer
   *
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=false)
   *
   * @Assert\NotNull()
   */
  private $requestedReviewBy;

  /**
   * If any, review comments on particular changes (per field) are stores here
   *
   * @var array|null
   *
   * @ORM\Column(type="json", nullable=true)
   *
   * @Assert\Type("array")
   */
  private $reviewComments;

  /**
   * Review constructor.
   */
  public function __construct()
  {
    $this->pendingChanges = new ArrayCollection();
  }

  /**
   * @return StudyArea
   */
  public function getStudyArea(): StudyArea
  {
    return $this->studyArea;
  }

  /**
   * @param StudyArea $studyArea
   *
   * @return Review
   */
  public function setStudyArea(StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  /**
   * @return User|null
   */
  public function getOwner(): ?User
  {
    return $this->owner;
  }

  /**
   * @param User|null $owner
   *
   * @return Review
   */
  public function setOwner(?User $owner): self
  {
    $this->owner = $owner;

    return $this;
  }

  /**
   * @return PendingChange[]|Collection
   */
  public function getPendingChanges()
  {
    return $this->pendingChanges;
  }

  /**
   * @param PendingChange $pendingChange
   *
   * @return Review
   */
  public function addPendingChange(PendingChange $pendingChange): Review
  {
    // Check whether the study area is set, otherwise set it as this
    if (!$pendingChange->getReview()) {
      $pendingChange->setReview($this);
    }
    $this->pendingChanges->add($pendingChange);

    return $this;
  }

  /**
   * @param PendingChange $PendingChange
   *
   * @return Review
   */
  public function removePendingChange(PendingChange $PendingChange): Review
  {
    $this->pendingChanges->removeElement($PendingChange);

    return $this;
  }

  /**
   * @return DateTime|null
   */
  public function getRequestedReviewAt(): ?DateTime
  {
    return $this->requestedReviewAt;
  }

  /**
   * @param DateTime|null $requestedReviewAt
   *
   * @return self
   */
  public function setRequestedReviewAt(?DateTime $requestedReviewAt): self
  {
    $this->requestedReviewAt = $requestedReviewAt;

    return $this;
  }

  /**
   * @return User|null
   */
  public function getRequestedReviewBy(): ?User
  {
    return $this->requestedReviewBy;
  }

  /**
   * @param User|null $requestedReviewBy
   *
   * @return self
   */
  public function setRequestedReviewBy(?User $requestedReviewBy): self
  {
    $this->requestedReviewBy = $requestedReviewBy;

    return $this;
  }

  /**
   * @return array|null
   */
  public function getReviewComments(): ?array
  {
    return $this->reviewComments;
  }

  /**
   * @param array|null $reviewComments
   *
   * @return self
   */
  public function setReviewComments(?array $reviewComments): self
  {
    $this->reviewComments = $reviewComments;

    return $this;
  }
}
