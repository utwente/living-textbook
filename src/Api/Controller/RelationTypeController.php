<?php

namespace App\Api\Controller;

use App\Api\Model\RelationType;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/relationtype") */
#[OA\Tag('Relation type')]
class RelationTypeController extends AbstractApiController
{
  /**
   * Retrieve all study area relation types
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area relation types', attachables: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: RelationType::class))),
  ])]
  public function list(RequestStudyArea $requestStudyArea, RelationTypeRepository $relationTypeRepository): JsonResponse
  {
    return $this->createDataResponse(array_map(
        [RelationType::class, 'fromEntity'],
        $relationTypeRepository->findForStudyArea($requestStudyArea->getStudyArea())
    ));
  }

  /**
   * Retrieve a single study area relation type
   *
   * @Route("/{relationType<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'A single study area relation type', attachables: [
      new Model(type: RelationType::class),
  ])]
  public function single(RequestStudyArea $requestStudyArea, \App\Entity\RelationType $relationType): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $relationType);

    return $this->createDataResponse(RelationType::fromEntity($relationType));
  }
}
