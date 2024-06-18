<?php

namespace App\Entity;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Repository\ReviewRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Interfaces\IdInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
#[ORM\Table]
class Review implements IdInterface
{
  use IdTrait;
  use Blameable;

  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(name: 'study_area_id', referencedColumnName: 'id', nullable: false)]
  private ?StudyArea $studyArea = null;

  /**
   * The pending changes in this review.
   *
   * @var Collection<PendingChange>
   */
  #[Assert\NotNull]
  #[Assert\Count(min: 1)]
  #[ORM\OneToMany(mappedBy: 'review', targetEntity: PendingChange::class, cascade: ['remove'])]
  #[ORM\OrderBy(['objectType' => 'ASC', 'changeType' => 'ASC'])]
  private Collection $pendingChanges;

  /** The owner of the pending change (aka, the user who created it). */
  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $owner = null;

  /** Notes left for the reviewer, if any. */
  #[Assert\Length(max: 2000)]
  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $notes = null;

  /** Requested datetime. */
  #[Assert\NotNull]
  #[ORM\Column]
  private ?DateTime $requestedReviewAt = null;

  /** The requested reviewer. */
  #[Assert\NotNull]
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $requestedReviewBy = null;

  /** Reviewed at datetime. */
  #[ORM\Column(nullable: true)]
  private ?DateTime $reviewedAt = null;

  /** Approved by, can be different than the requested reviewer. */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: true)]
  private ?User $reviewedBy = null;

  /** Approval datetime. */
  #[ORM\Column(nullable: true)]
  private ?DateTime $approvedAt = null;

  /** Approved by, can be different than the requested reviewer. */
  #[ORM\ManyToOne]
  #[ORM\JoinColumn(nullable: true)]
  private ?User $approvedBy = null;

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
