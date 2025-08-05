<?php

namespace App\Export;

use App\Entity\StudyArea;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\HttpFoundation\Response;

#[AutoconfigureTag('app.export_provider')]
interface ProviderInterface
{

  /**
   * Provider must be tagged as an item and supply an index. The index value is used to derive the translation
   * key.
   *
   * @deprecated Use the index attribute of #AsTaggedItem instead. For more details see:
   *             https://symfony.com/doc/6.4/service_container/tags.html#tagged-services-with-index
   */
  #[\Deprecated(message: 'Use the index attribute of #AsTaggedItem instead.')]
  public function getName(): string;

  /** Provider preview, based on single concept from study area. */
  public function getPreview(): string;

  /** Export the data, and return a response. */
  public function export(StudyArea $studyArea): Response;
}
