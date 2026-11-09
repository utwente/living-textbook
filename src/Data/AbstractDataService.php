<?php

namespace App\Data;

use App\Entity\StudyArea;

use Symfony\Contracts\Service\ServiceProviderInterface;

use function array_combine;
use function array_filter;
use function array_keys;
use function array_map;

/** @template TProvider of ProviderInterface */
abstract readonly class AbstractDataService
{
  /** @param ServiceProviderInterface<TProvider> $providers */
  public function __construct(protected ServiceProviderInterface $providers)
  {
  }

  /**
   * Retrieve the available export provider keys.
   *
   * @return string[]
   */
  public function getAvailableProviderKeys(StudyArea $studyArea): array
  {
    return array_filter(
      array_keys($this->providers->getProvidedServices()),
      fn (string $k): bool => $this->providers->get($k)->isAvailable($studyArea),
    );
  }

  /**
   * Retrieve the previews from the providers.
   *
   * @return string[]
   */
  public function getPreviews(StudyArea $studyArea): array
  {
    $providerKeys = $this->getAvailableProviderKeys($studyArea);

    return array_combine(
      $providerKeys,
      array_map(fn (string $k): string => $this->providers->get($k)->getPreview($studyArea), $providerKeys),
    );
  }
}
