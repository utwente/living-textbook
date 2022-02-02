<?php

namespace App\Api\Controller;

use App\Api\Model\RelationType;
use App\Api\Model\Validation\ValidationFailedData;
use App\EntityHandler\RelationTypeHandler;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
  #[OA\Response(response: 200, description: 'All study area relation types', content: [
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
  #[OA\Response(response: 200, description: 'A single study area relation type', content: [
      new Model(type: RelationType::class),
  ])]
  public function single(RequestStudyArea $requestStudyArea, \App\Entity\RelationType $relationType): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $relationType);

    return $this->createDataResponse(RelationType::fromEntity($relationType));
  }

  /**
   * Add a new study area relation type
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(content: [new Model(type: RelationType::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The new tag', content: [new Model(type: RelationType::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
      RequestStudyArea $requestStudyArea,
      Request          $request): JsonResponse
  {
    $relationType = $this->getTypedFromBody($request, RelationType::class)
        ->mapToEntity(NULL)
        ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($relationType);

    return $this->createDataResponse(RelationType::fromEntity($relationType));
  }

  /**
   * Update an existing study area relation type
   *
   * @Route("/{relationType<\d+>}", methods={"PATCH"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(content: [new Model(type: RelationType::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated relation type', content: [new Model(type: RelationType::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]

  public function update(
      RequestStudyArea         $requestStudyArea,
      \App\Entity\RelationType $relationType,
      Request                  $request
  ): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $relationType);

    if ($relationType->isDeleted()) {
      throw $this->createNotFoundException();
    }

    $relationType = $this->getTypedFromBody($request, RelationType::class)
        ->mapToEntity($relationType);

    $this->getHandler()->update($relationType);

    return $this->createDataResponse(RelationType::fromEntity($relationType));
  }

  /**
   * Delete an existing study area relation type
   *
   * @Route("/{relationType<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The relation type has been deleted')]
  public function delete(
      RequestStudyArea         $requestStudyArea,
      \App\Entity\RelationType $relationType): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $relationType);

    $this->getHandler()->delete($relationType, $this->getUser());

    return new JsonResponse(NULL, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): RelationTypeHandler
  {
    return new RelationTypeHandler($this->em, $this->validator, NULL);
  }
}
