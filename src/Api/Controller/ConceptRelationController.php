<?php

namespace App\Api\Controller;

use App\Api\Model\Detailed\DetailedConceptRelation;
use App\Entity\ConceptRelation;
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
   * Retrieve all study area concept relations
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area concept relations', content: [
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

  /**
   * Retrieve a single study area concept relation
   *
   * @Route("/{conceptRelation<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'Single study area concept relation')]
  public function single(RequestStudyArea $requestStudyArea, ConceptRelation $conceptRelation,): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource() ?? $conceptRelation->getTarget());

    return $this->createDataResponse(DetailedConceptRelation::fromEntity($conceptRelation));
  }

  // todo: CRUD
}
