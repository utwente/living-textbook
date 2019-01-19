<?php

namespace App\Export;

use App\Entity\StudyArea;
use App\Export\Provider\ConceptIdNameProvider;
use App\Export\Provider\LinkedSimpleNodeProvider;
use App\Export\Provider\RelationProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ExportService
{
  /**
   * Available export types, and their providers
   *
   * @var ProviderInterface[]
   */
  private $providers = [];

  public function __construct(LinkedSimpleNodeProvider $p1, ConceptIdNameProvider $p2, RelationProvider $p3)
  {
    $key = 0;
    foreach (func_get_args() as $provider) {
      /** @var ProviderInterface $provider */
      $this->providers['p' . ++$key] = $provider;
    }
  }

  /**
   * Get the available choices, to be used in a ChoiceType
   *
   * @return array
   */
  public function getChoices()
  {
    $data = [];
    foreach ($this->providers as $key => $provider) {
      $providerName = strtolower($provider->getName());
      $choiceName   = 'data.download.provider.' . $providerName;
      if (array_key_exists($choiceName, $data)) {
        throw new \InvalidArgumentException("Non-unique download providers registered");
      }
      $data[$choiceName] = $key;
    }

    ksort($data);

    return $data;
  }

  /**
   * Retrieve the previews from the providers
   *
   * @return array
   */
  public function getPreviews(): array
  {
    $data = [];
    foreach ($this->providers as $key => $provider) {
      $data[$key] = $provider->getPreview();
    }

    return $data;
  }

  /**
   * Retrieve the export data
   *
   * @param StudyArea $studyArea
   * @param string    $exportProvider
   *
   * @return Response
   */
  public function export(StudyArea $studyArea, string $exportProvider): Response
  {
    if (!array_key_exists($exportProvider, $this->providers)) {
      throw new \InvalidArgumentException(sprintf("Requested provider %s is not registered!", $exportProvider));
    }

    return $this->providers[$exportProvider]->export($studyArea);
  }

  /**
   * Create a correct content disposition
   *
   * @param Response $response
   * @param string   $filename
   */
  public static function contentDisposition(Response $response, string $filename): void
  {
    $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        mb_strtolower(preg_replace('/[^\p{L}\p{N}.]/u', '_', $filename))
    ));
  }
}
