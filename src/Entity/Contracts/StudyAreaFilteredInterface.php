<?php

namespace App\Entity\Contracts;

use App\Entity\StudyArea;

/**
 * Interface StudyAreaFilteredInterface.
 *
 * Marks an object as filtered by an study area
 */
interface StudyAreaFilteredInterface
{
  /** Retrieves the object id. */
  public function getId(): ?int;

  /** Retrieves the study area. */
  public function getStudyArea(): ?StudyArea;
}
