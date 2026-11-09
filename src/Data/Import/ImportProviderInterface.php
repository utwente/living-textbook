<?php

namespace App\Data\Import;

use App\Data\ProviderInterface;
use App\Entity\StudyArea;
use App\Exception\DataImportException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag]
interface ImportProviderInterface extends ProviderInterface
{
  /**
   * Imports the posted data.
   *
   * @param array<mixed> $jsonData the JSON data to import
   *
   * @throws DataImportException
   */
  public function import(StudyArea $studyArea, array $jsonData): void;
}
