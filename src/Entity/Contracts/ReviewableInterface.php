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
   * Get the fields that must be considered for review
   *
   * @return string[]
   */
  public function getReviewFieldsNames(): array;

}
