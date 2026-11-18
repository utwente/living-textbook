<?php

namespace App\Data\Export;

use App\Data\ProviderInterface;
use App\Entity\StudyArea;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag]
interface ExportProviderInterface extends ProviderInterface
{
  /** Export the data, and return a response. */
  public function export(StudyArea $studyArea): Response;

  /** Provider preview, based on single concept from study area. */
  public function getPreview(StudyArea $studyArea): string;
}
