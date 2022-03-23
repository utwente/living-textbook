<?php

namespace App\Export;

use App\Entity\StudyArea;
use Symfony\Component\HttpFoundation\Response;

interface ProviderInterface
{
  /** Provider name, must be unique. */
  public function getName(): string;

  /** Provider preview, based on single concept from study area. */
  public function getPreview(): string;

  /** Export the data, and return a response. */
  public function export(StudyArea $studyArea): Response;
}
