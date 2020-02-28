<?php


namespace App\Entity\Contracts;

use App\Entity\PendingChange;
use App\Entity\StudyArea;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Interface IReviewable
 * Marks the entity as reviewable
 */
interface ReviewableInterface extends StudyAreaFilteredInterface
{

  /**
   * Apply the changes as specified
   *
   * @param PendingChange          $change
   * @param EntityManagerInterface $em
   * @param bool                   $ignoreEm
   */
  public function applyChanges(PendingChange $change, EntityManagerInterface $em, bool $ignoreEm = false): void;

  /**
   * The name used in the pending change table to store the change
   * Must be unique per entity
   *
   * @return string
   */
  public function getReviewName(): string;

  /**
   * The title of the object, used to identify the object
   *
   * @return string
   */
  public function getReviewTitle(): string;

  /**
   * Set the study area
   *
   * @param StudyArea $studyArea
   *
   * @return mixed
   */
  public function setStudyArea(StudyArea $studyArea);
}
