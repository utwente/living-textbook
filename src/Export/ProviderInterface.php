<?php

namespace App\Export;

use App\Entity\StudyArea;
use Symfony\Component\HttpFoundation\Response;

interface ProviderInterface
{
  /**
   * Provider name, must be unique
   *
   * @return string
   */
  public function getName(): string;

  /**
   * Provider preview, based on single concept from study area
   *
   * @return string
   */
  public function getPreview(): string;

  /**
   * Export the data, and return a response
   *
   * @param StudyArea $studyArea
   *
   * @return Response
   */
  public function export(StudyArea $studyArea): Response;
}
