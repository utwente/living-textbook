<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\StudyArea;
use App\Export\ExportService;
use App\Export\ProviderInterface;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\RelationTypeRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LinkedSimpleNodeProvider implements ProviderInterface
{
  /**
   * @var ConceptRepository
   */
  private $conceptRepository;
  /**
   * @var ConceptRelationRepository
   */
  private $conceptRelationRepository;
  /**
   * @var ExternalResourceRepository
   */
  private $externalResourceRepository;
  /**
   * @var RelationTypeRepository
   */
  private $relationTypeRepository;
  /**
   * @var SerializerInterface
   */
  private $serializer;

  public function __construct(
      ConceptRepository $conceptRepository, ConceptRelationRepository $conceptRelationRepository,
      ExternalResourceRepository $externalResourceRepository,
      RelationTypeRepository $relationTypeRepository, SerializerInterface $serializer)
  {
    $this->conceptRepository          = $conceptRepository;
    $this->conceptRelationRepository  = $conceptRelationRepository;
    $this->externalResourceRepository = $externalResourceRepository;
    $this->relationTypeRepository     = $relationTypeRepository;
    $this->serializer                 = $serializer;
  }

  /**
   * @inheritdoc
   */
  public function getName(): string
  {
    return 'linked-simple-node';
  }

  /**
   * @inheritdoc
   */
  public function getPreview(): string
  {
    return <<<'EOT'
{
    "nodes": [
        {
            "numberOfLinks": <number-of-relations>,
            "label": "<concept-name>",
            "link": "<concept-url>"
            "definition": "<concept-definition>"
        }
    ],
    "links": [
        {
            "target": <target-id>,
            "source": <source-id>,
            "relationName": "<relation-name>"
        }
    ],
    "external_resources": [
        {
            "nodes": [<node-ids>],
            "title": "<external-resource-title>",
            "description": "<external-resource-description>",
            "url": "<external-resource-url>",
        }
    ]
}
EOT;
  }

  /**
   * @inheritdoc
   */
  public function export(StudyArea $studyArea): Response
  {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $this->relationTypeRepository->findBy(['studyArea' => $studyArea]);

    // Retrieve the concepts
    $concepts          = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);
    $links             = $this->conceptRelationRepository->findByConcepts($concepts);
    $externalResources = $this->externalResourceRepository->findForStudyAreaOrderedByTitle($studyArea);

    // Detach the data from the ORM
    $idMap = [];
    foreach ($concepts as $key => $concept) {
      $idMap[$concept->getId()] = $key;
    }

    // Create link data
    $mappedLinks = [];
    foreach ($links as &$link) {
      $mappedLinks[] = [
          'target'       => $idMap[$link->getTargetId()],
          'source'       => $idMap[$link->getSourceId()],
          'relationName' => $link->getRelationName(),
      ];
    }

    // Create external resource data
    $mappedExternalResources = [];
    foreach ($externalResources as $externalResource) {
      $mappedExternalResources[] = [
          'nodes'       => $externalResource->getConcepts()->map(function (Concept $concept) use ($idMap) {
            return $idMap[$concept->getId()];
          }),
          'title'       => $externalResource->getTitle(),
          'description' => $externalResource->getDescription(),
          'url'         => $externalResource->getUrl(),
      ];
    }

    // Create JSON data
    {
      // Return as JSON
      $json = $this->serializer->serialize(
          [
              'nodes'              => $concepts,
              'links'              => $mappedLinks,
              'external_resources' => $mappedExternalResources,
          ],
          'json',
          /** @phan-suppress-next-line PhanTypeMismatchArgument */
          SerializationContext::create()->setGroups(['download_json']));

      $response = new JsonResponse($json, Response::HTTP_OK, [], true);
      ExportService::contentDisposition($response, sprintf('%s_export.json', $studyArea->getName()));

      return $response;
    }
  }
}
