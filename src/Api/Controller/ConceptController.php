<?php

namespace App\Api\Controller;

use App\Api\Model\Concept;
use App\Repository\ConceptRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/concept") */
#[OA\Tag('Concept')]
class ConceptController extends AbstractApiController
{
  /**
   * Retrieve all study area concepts
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area concepts', attachables: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: Concept::class))),
  ])]
  public function list(RequestStudyArea $requestStudyArea, ConceptRepository $conceptRepository): JsonResponse
  {
    return $this->createDataResponse(array_map(
        [Concept::class, 'fromEntity'],
        $conceptRepository->findForStudyAreaOrderedByName($requestStudyArea->getStudyArea(), conceptsOnly: true)
    ));
  }

  /**
   * Retrieve a single study area concept
   *
   * @Route("/{concept<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'A single study area concept', attachables: [
      new Model(type: Concept::class),
  ])]
  public function single(RequestStudyArea $requestStudyArea, \App\Entity\Concept $concept): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    return $this->createDataResponse(Concept::fromEntity($concept));
  }

  // todo: CRUD
}
