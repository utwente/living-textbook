<?php

namespace App\Data\Import;

use App\Data\AbstractDataService;
use App\Entity\StudyArea;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Contracts\Service\ServiceProviderInterface;

use function ini_set;
use function set_time_limit;
use function sprintf;

/** @extends AbstractDataService<ImportProviderInterface> */
#[Lazy]
final readonly class ImportService extends AbstractDataService
{
  /** @param ServiceProviderInterface<ImportProviderInterface> $providers The registered export providers */
  public function __construct(
    #[AutowireLocator(ImportProviderInterface::class, defaultIndexMethod: 'getName')]
    ServiceProviderInterface $providers,
  ) {
    parent::__construct($providers);
  }

  /** @param array<string, mixed> $jsonData */
  public function import(StudyArea $studyArea, string $importProvider, array $jsonData): void
  {
    if (!$this->providers->has($importProvider)) {
      throw new InvalidArgumentException(sprintf('Requested provider %s is not registered!', $importProvider));
    }

    // Allow for more memory and time usage during import
    ini_set('memory_limit', '512M');
    set_time_limit(300);

    $this->providers->get($importProvider)->import($studyArea, $jsonData);
  }
}
