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
  public static function getName(): string;

  /**
   * Export the data, and return a response
   *
   * @param StudyArea $studyArea
   *
   * @return Response
   */
  public function export(StudyArea $studyArea): Response;
}
