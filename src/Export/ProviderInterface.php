<?php

namespace App\Export;

use App\Entity\StudyArea;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag]
interface ProviderInterface
{
  /** Provider name, must be unique. */
  public static function getName(): string;

  /** Whether the exporter is enabled for the study area. */
  public function isAvailable(StudyArea $studyArea): bool;

  /** Provider preview, based on single concept from study area. */
  public function getPreview(StudyArea $studyArea): string;

  /** Export the data, and return a response. */
  public function export(StudyArea $studyArea): Response;
}
