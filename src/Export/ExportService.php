<?php

namespace App\Export;

use App\Entity\StudyArea;
use InvalidArgumentException;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\ChoiceListInterface;
use Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use function array_combine;
use function array_key_exists;
use function array_keys;
use function array_unique;
use function iterator_to_array;
use function array_map;
use function iconv;
use function ksort;
use function mb_strtolower;
use function preg_replace;
use function setlocale;
use function sprintf;
use function strtolower;

use const LC_CTYPE;

class ExportService implements ChoiceLoaderInterface
{

  /*
   * The default translation prefix.
   */
  public const string TRANSLATION_PREFIX = 'data.download.provider';

  /**
   * Available export types, and their providers.
   *
   * @var ProviderInterface[]
   */
  private array $providers;

  /**
   * The {@link ProviderInterface} tagged index is prefixed with this value when no other value is provided
   * during instantiation.
   * 
   * @var string
   */
  private readonly string $transPrefix;

  /**
   * ExportService constructor.
   *
   * Initializes the export service with provided export providers and an optional translation prefix.
   * The providers are stored in a normalized format with keys prefixed with the translation prefix.
   *
   * @param iterable    $providers   Array or Traversable of export providers to be registered
   * @param string|null $transPrefix Optional prefix for translation keys, defaults to TRANSLATION_PREFIX
   */
  public function __construct(iterable $providers, ?string $transPrefix = null)
  {
    $this->transPrefix = $transPrefix ?? self::TRANSLATION_PREFIX;
    $providers = array_unique($providers instanceof \Traversable ? iterator_to_array($providers) : $providers, SORT_REGULAR);
    ksort($providers);
    $this->providers = [];
    foreach($providers as $key => $provider) {
      $this->providers[strtolower($this->transPrefix . '.' . $key)] = $provider;
    }
  }

  /**
   * Retrieve the previews from the providers.
   *
   * @return string[]
   */
  public function getPreviews(): array
  {
    $data = [];
    foreach ($this->providers as $key => $provider) {
      $data[$key] = $provider->getPreview();
    }

    return $data;
  }

  /** Retrieve the export data. */
  public function export(StudyArea $studyArea, string $exportProvider): Response
  {
    if (!array_key_exists($exportProvider, $this->providers)) {
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
      mb_strtolower((string)preg_replace('/[^A-Z\d.]/ui', '_', iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename)))
    ));
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function loadChoiceList(?callable $value = null): ChoiceListInterface
  {
    $keys = array_keys($this->providers);
    $choices = array_combine($keys, $keys);
    return new ArrayChoiceList($choices, $value);
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function loadChoicesForValues(array $values, ?callable $value = null): array
  {
    if (!$values) {
      return [];
    }
    return $this->loadChoiceList($value)->getValuesForChoices($values);
  }

  /**
   * {@inheritDoc}
   */
  #[\Override]
  public function loadValuesForChoices(array $choices, ?callable $value = null): array
  {
    if (!$choices) {
      return [];
    }
    if ($value) {
      return array_map(fn ($item) => (string) $value($item), $choices);
    }
    return $this->loadChoiceList()->getValuesForChoices($choices);
  }
}
