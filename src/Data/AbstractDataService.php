<?php

namespace App\Data;

use App\Entity\StudyArea;
use Symfony\Contracts\Service\ServiceProviderInterface;

use function array_filter;
use function array_keys;

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
}
