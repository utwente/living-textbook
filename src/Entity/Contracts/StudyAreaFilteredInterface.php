<?php

namespace App\Entity\Contracts;

use App\Entity\StudyArea;

/**
 * Interface StudyAreaFilteredInterface
 *
 * Marks an object as filtered by an study area
 */
interface StudyAreaFilteredInterface
{
  function getId(): ?int;

  function getStudyArea(): ?StudyArea;
}
