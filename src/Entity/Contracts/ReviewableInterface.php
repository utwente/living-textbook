<?php


namespace App\Entity\Contracts;

/**
 * Interface IReviewable
 * Marks the entity as reviewable
 */
interface ReviewableInterface
{

  /**
   * Retrieves the object id
   *
   * @return int|null
   */
  public function getId(): ?int;

  /**
   * The name used in the pending change table to store the change
   * Must be unique per entity
   *
   * @return string
   */
  public function getReviewName(): string;

  /**
   * Get the field names that must be considered for review.
   *
   * These can only be simple fields or ManyToOne associations with object that implement
   * this interface as well.
   *
   * @return string[]
   */
  public function getReviewFieldNames(): array;

  /**
   * Get the relation field names that must only be considered review. Only the id property is used for the check.
   *
   * Only a single id of the relation can/will be checked, which means only ManyToOne associations will work.
   *
   * @return array
   */
  public function getReviewIdFieldNames(): array;

}
