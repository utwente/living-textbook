<?php

namespace App\Data\Export;

use App\Data\AbstractDataService;
use App\Entity\StudyArea;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Contracts\Service\ServiceProviderInterface;

use function iconv;
use function mb_strtolower;
use function preg_replace;
use function setlocale;
use function sprintf;

use const LC_CTYPE;

/** @extends AbstractDataService<ExportProviderInterface> */
#[Lazy]
final readonly class ExportService extends AbstractDataService
{
  /** @param ServiceProviderInterface<ExportProviderInterface> $providers The registered export providers */
  public function __construct(
    #[AutowireLocator(ExportProviderInterface::class, defaultIndexMethod: 'getName')]
    ServiceProviderInterface $providers,
  ) {
    parent::__construct($providers);
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
    $filename = mb_strtolower((string)preg_replace(
      '/[^A-Z\d.]/ui',
      '_',
      (string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename),
    ));
    $response->headers->set(
      'Content-Disposition',
      $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename),
    );
  }
}
