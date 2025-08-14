<?php

namespace App\Export;

use App\Entity\StudyArea;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag]
interface ProviderInterface
{
  /** Provider preview, based on single concept from study area. */
  public function getPreview(): string;

  /** Export the data, and return a response. */
  public function export(StudyArea $studyArea): Response;
}
