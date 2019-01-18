<?php

namespace App\Export\Provider;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\StudyArea;
use App\Export\ProviderInterface;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class LinkedSimpleNodeProvider implements ProviderInterface
{
  /** @var ConceptRepository */
  private $conceptRepository;
  /** @var ConceptRelationRepository */
  private $conceptRelationRepository;
  /** @var RelationTypeRepository */
  private $relationTypeRepository;
  /** @var SerializerInterface */
  private $serializer;

  public function __construct(ConceptRepository $conceptRepository, ConceptRelationRepository $conceptRelationRepository,
                              RelationTypeRepository $relationTypeRepository, SerializerInterface $serializer)
  {
    $this->conceptRepository         = $conceptRepository;
    $this->conceptRelationRepository = $conceptRelationRepository;
    $this->relationTypeRepository    = $relationTypeRepository;
    $this->serializer                = $serializer;
  }

  public static function getName(): string
  {
    return 'linked-simple-node';
  }

  /**
   * Export the data, and return a response
   *
   * @param StudyArea $studyArea
   *
   * @return Response
   */
  public function export(StudyArea $studyArea): Response
  {
    /** @noinspection PhpUnusedLocalVariableInspection Retrieve the relation types as cache */
    $relationTypes = $this->relationTypeRepository->findBy(['studyArea' => $studyArea]);

    // Retrieve the concepts
    $concepts = $this->conceptRepository->findForStudyAreaOrderedByName($studyArea);
    $links    = $this->conceptRelationRepository->findByConcepts($concepts);

    // Detach the data from the ORM
    $idMap = [];
    foreach ($concepts as $key => $concept) {
      assert($concept instanceof Concept);
      $idMap[$concept->getId()] = $key;
    }
    $mappedLinks = [];
    foreach ($links as &$link) {
      assert($link instanceof ConceptRelation);
      $mappedLinks[] = [
          'target'       => $idMap[$link->getTargetId()],
          'source'       => $idMap[$link->getSourceId()],
          'relationName' => $link->getRelationName(),
      ];
    }

    // Create JSON data
    {
      // Return as JSON
      $json = $this->serializer->serialize([
          'nodes' => $concepts,
          'links' => $mappedLinks,
      ], 'json', SerializationContext::create()->setGroups(['download_json']));

      $response = new JsonResponse($json, Response::HTTP_OK, [], true);
      $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
          ResponseHeaderBag::DISPOSITION_ATTACHMENT,
          sprintf('%s_export.json', mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '_', $studyArea->getName())))
      ));

      return $response;
    }
  }
}
