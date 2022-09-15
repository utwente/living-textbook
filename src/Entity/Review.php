<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Review.
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="App\Repository\ReviewRepository")
 */
class Review implements IdInterface
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
   * The pending changes in this review.
   *
   * @var Collection<PendingChange>
   *
   * @ORM\OneToMany(targetEntity="App\Entity\PendingChange", mappedBy="review", cascade={"remove"})
   * @ORM\OrderBy({"objectType" = "ASC", "changeType" = "ASC"})
   *
   * @Assert\NotNull()
   * @Assert\Count(min=1)
   */
  private $pendingChanges;

  /**
   * The owner of the pending change (aka, the user who created it).
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
   * Notes left for the reviewer, if any.
   *
   * @var string|null
   *
   * @ORM\Column(type="text", nullable=true)
   *
   * @Assert\Length(max=2000)
   */
  private $notes;

  /**
   * Requested datetime.
   *
   * @var DateTime|null
   *
   * @ORM\Column(type="datetime")
   *
   * @Assert\NotNull()
   * @Assert\Type("datetime")
   */
  private $requestedReviewAt;

  /**
   * The requested reviewer.
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
   * Reviewed at datetime.
   *
   * @var DateTime|null
   *
   * @ORM\Column(type="datetime", nullable=true)
   *
   * @Assert\Type("datetime")
   */
  private $reviewedAt;

  /**
   * Approved by, can be different than the requested reviewer.
   *
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=true)
   */
  private $reviewedBy;

  /**
   * Approval datetime.
   *
   * @var DateTime|null
   *
   * @ORM\Column(type="datetime", nullable=true)
   *
   * @Assert\Type("datetime")
   */
  private $approvedAt;

  /**
   * Approved by, can be different than the requested reviewer.
   *
   * @var User|null
   *
   * @ORM\ManyToOne(targetEntity="App\Entity\User")
   * @ORM\JoinColumn(nullable=true)
   */
  private $approvedBy;

  /** Review constructor. */
  public function __construct()
  {
    $this->pendingChanges = new ArrayCollection();
  }

  /** Retrieve whether the review has comments set somewhere. */
  public function hasComments(): bool
  {
    foreach ($this->getPendingChanges() as $pendingChange) {
      if (0 !== count($pendingChange->getReviewComments() ?? [])) {
        return true;
      }
    }

    return false;
  }

  public function getStudyArea(): StudyArea
  {
    return $this->studyArea;
  }

  public function setStudyArea(StudyArea $studyArea): self
  {
    $this->studyArea = $studyArea;

    return $this;
  }

  public function getOwner(): ?User
  {
    return $this->owner;
  }

  public function setOwner(?User $owner): self
  {
    $this->owner = $owner;

    return $this;
  }

  public function getNotes(): ?string
  {
    return $this->notes;
  }

  public function setNotes(?string $notes): self
  {
    $this->notes = $notes;

    return $this;
  }

  /** @return Collection<PendingChange> */
  public function getPendingChanges(): Collection
  {
    return $this->pendingChanges;
  }

  public function addPendingChange(PendingChange $pendingChange): Review
  {
    // Check whether the study area is set, otherwise set it as this
    if (!$pendingChange->getReview()) {
      $pendingChange->setReview($this);
    }
    $this->pendingChanges->add($pendingChange);

    return $this;
  }

  public function removePendingChange(PendingChange $PendingChange): Review
  {
    $this->pendingChanges->removeElement($PendingChange);

    return $this;
  }

  public function getRequestedReviewAt(): ?DateTime
  {
    return $this->requestedReviewAt;
  }

  public function setRequestedReviewAt(?DateTime $requestedReviewAt): self
  {
    $this->requestedReviewAt = $requestedReviewAt;

    return $this;
  }

  public function getRequestedReviewBy(): ?User
  {
    return $this->requestedReviewBy;
  }

  public function setRequestedReviewBy(?User $requestedReviewBy): self
  {
    $this->requestedReviewBy = $requestedReviewBy;

    return $this;
  }

  public function getReviewedAt(): ?DateTime
  {
    return $this->reviewedAt;
  }

  public function setReviewedAt(?DateTime $reviewedAt): Review
  {
    $this->reviewedAt = $reviewedAt;

    return $this;
  }

  public function getReviewedBy(): ?User
  {
    return $this->reviewedBy;
  }

  public function setReviewedBy(?User $reviewedBy): Review
  {
    $this->reviewedBy = $reviewedBy;

    return $this;
  }

  public function getApprovedAt(): ?DateTime
  {
    return $this->approvedAt;
  }

  public function setApprovedAt(?DateTime $approvedAt): self
  {
    $this->approvedAt = $approvedAt;

    return $this;
  }

  public function getApprovedBy(): ?User
  {
    return $this->approvedBy;
  }

  public function setApprovedBy(?User $approvedBy): self
  {
    $this->approvedBy = $approvedBy;

    return $this;
  }
}
