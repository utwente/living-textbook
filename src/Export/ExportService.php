<?php

namespace App\Export;

use App\Entity\StudyArea;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use function array_unique;
use function iterator_to_array;
use function array_map;
use function iconv;
use function ksort;
use function mb_strtolower;
use function preg_replace;
use function setlocale;
use function sprintf;

use const LC_CTYPE;

class ExportService
{

  /**
   * Available export types, and their providers.
   *
   * @var ProviderInterface[]
   */
  private array $providers;


  /**
   * ExportService constructor.
   *
   * @param iterable<ProviderInterface> $providers Array or Traversable of export providers to be registered
   */
  public function __construct(iterable $providers)
  {
    $this->providers = array_unique($providers instanceof \Traversable ? iterator_to_array($providers) : $providers, SORT_REGULAR);
    ksort($this->providers);
  }

  /**
   * Retrieve the previews from the providers.
   *
   * @return string[]
   */
  public function getPreviews(): array
  {
    return array_map(fn (ProviderInterface $provider) => $provider->getPreview(), $this->providers);
  }

  /** Retrieve the export data. */
  public function export(StudyArea $studyArea, string $exportProvider): Response
  {
    $provider = $this->getProvider($exportProvider);
    if (null === $provider) {
      throw new InvalidArgumentException(sprintf('Requested provider %s is not registered!', $exportProvider));
    }

    return $this->providers[$exportProvider]->export($studyArea);
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

  /**
   * Get the registered providers sorted by their index value.
   *
   * @return array|ProviderInterface[]
   */
  public function getProviders(): array
  {
    return $this->providers;
  }

  /**
   * Get the provider for the `$index` or null if it doesn't exist.
   *
   * The index is the value used in the {@link Symfony\Component\DependencyInjection\Attribute\AsTaggedItem::index}
   * attribute of the {@link App\Export\Provider\ProviderInterface} class.
   *
   * @param string $index
   *
   * @return ProviderInterface|null
   */
  public function getProvider(string $index): ?ProviderInterface
  {
    return $this->providers[$index] ?? null;
  }

}
