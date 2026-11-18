<?php

namespace App\Data;

use App\Entity\StudyArea;

interface ProviderInterface
{
  /** Provider name, must be unique. */
  public static function getName(): string;

  /** Whether the exporter is enabled for the study area. */
  public function isAvailable(StudyArea $studyArea): bool;
}
