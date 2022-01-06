<?php

namespace App\Api\Controller;

use App\Api\Model\Detailed\DetailedConceptRelation;
use App\Repository\ConceptRelationRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/conceptrelation") */
#[OA\Tag('Concept relation')]
class ConceptRelationController extends AbstractApiController
{
  /**
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'Retrieve all study area concept relations', attachables: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: DetailedConceptRelation::class))),
  ])]
  public function list(
      RequestStudyArea          $requestStudyArea,
      ConceptRelationRepository $conceptRelationRepository): JsonResponse
  {
    return $this->createDataResponse(array_map(
        [DetailedConceptRelation::class, 'fromEntity'],
        $conceptRelationRepository->getByStudyArea($requestStudyArea->getStudyArea())
    ));
  }

  // todo: CRUD
}
