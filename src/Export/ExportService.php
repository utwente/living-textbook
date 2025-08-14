<?php

namespace App\Export;

use App\Entity\StudyArea;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use function array_combine;
use function array_keys;
use function array_map;
use function iconv;
use function mb_strtolower;
use function preg_replace;
use function setlocale;
use function sprintf;

use const LC_CTYPE;

#[Autoconfigure(lazy: true)]
readonly class ExportService
{
  /** @param ServiceLocator<ProviderInterface> $providers The registered export providers */
  public function __construct(
    #[AutowireLocator(ProviderInterface::class, defaultIndexMethod: 'getName')]
    private ServiceLocator $providers,
  ) {
  }

  /**
   * Retrieve the available export provider keys.
   *
   * @return string[]
   */
  public function getAvailableProviderKeys(): array
  {
    return array_keys($this->providers->getProvidedServices());
  }

  /**
   * Retrieve the previews from the providers.
   *
   * @return string[]
   */
  public function getPreviews(): array
  {
    $providerKeys = $this->getAvailableProviderKeys();

    return array_combine(
      $providerKeys,
      array_map(fn (string $k): string => $this->providers->get($k)->getPreview(), $providerKeys),
    );
  }

  /** Retrieve the export data. */
  public function export(StudyArea $studyArea, string $exportProvider): Response
  {
    if (!$this->providers->has($exportProvider)) {
      throw new InvalidArgumentException(sprintf('Requested provider %s is not registered!', $exportProvider));
    }

    return $this->providers->get($exportProvider)->export($studyArea);
  }

  /** Create a correct content disposition. */
  public static function contentDisposition(Response $response, string $filename): void
  {
    // Set locale required for the iconv conversion to work correctly
    setlocale(LC_CTYPE, 'en_US.UTF-8');
    $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
      ResponseHeaderBag::DISPOSITION_ATTACHMENT,
      mb_strtolower((string)preg_replace('/[^A-Z\d.]/ui', '_', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename))),
    ));
  }
}
