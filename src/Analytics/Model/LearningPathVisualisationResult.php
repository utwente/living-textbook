<?php

namespace App\Analytics\Model;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class LearningPathVisualisationResult.
 *
 * Serialization of this class is managed in the LearningPathVisualisationResultHandler
 *
 * @Serializer\ExclusionPolicy("all")
 */
class LearningPathVisualisationResult
{
  /** @var SplFileInfo */
  public $heatMapImage;

  /** @var SplFileInfo */
  public $pathVisitsImage;

  /** @var SplFileInfo */
  public $pathUsersImage;

  /** @var SplFileInfo */
  public $flowThroughFile;

  /** @var SplFileInfo */
  public $metaDataFile;

  /** @return string */
  public function getFlowThroughFileJson(): string
  {
    return $this->flowThroughFile->getContents();
  }
}
