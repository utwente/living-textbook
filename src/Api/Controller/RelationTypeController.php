<?php

namespace App\Api\Controller;

use App\Api\Model\RelationTypeApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\RelationType;
use App\EntityHandler\RelationTypeHandler;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag('Relation type')]
#[Route('/relationtype')]
class RelationTypeController extends AbstractApiController
{
  /** Retrieve all study area relation types. */
  #[OA\Response(response: 200, description: 'All study area relation types', content: [
    new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: RelationTypeApiModel::class))),
  ])]
  #[Route(methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(RequestStudyArea $requestStudyArea, RelationTypeRepository $relationTypeRepository): JsonResponse
  {
    return $this->createDataResponse(array_map(
      [RelationTypeApiModel::class, 'fromEntity'],
      $relationTypeRepository->findForStudyArea($requestStudyArea->getStudyArea())
    ));
  }

  /** Retrieve a single study area relation type. */
  #[OA\Response(response: 200, description: 'A single study area relation type', content: [
    new Model(type: RelationTypeApiModel::class),
  ])]
  #[Route('/{relationType<\d+>}', methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function single(RequestStudyArea $requestStudyArea, RelationType $relationType): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $relationType);

    return $this->createDataResponse(RelationTypeApiModel::fromEntity($relationType));
  }

  /** Add a new study area relation type. */
  #[OA\RequestBody(description: 'The new relation type', required: true, content: [new Model(type: RelationTypeApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The new relation type', content: [new Model(type: RelationTypeApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: [Request::METHOD_POST])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function add(
    RequestStudyArea $requestStudyArea,
    Request $request): JsonResponse
  {
    $relationType = $this->getTypedFromBody($request, RelationTypeApiModel::class)
      ->mapToEntity(null)
      ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($relationType);

    return $this->createDataResponse(RelationTypeApiModel::fromEntity($relationType));
  }

  /** Update an existing study area relation type. */
  #[OA\RequestBody(description: 'The relation type properties to update', required: true, content: [new Model(type: RelationTypeApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated relation type', content: [new Model(type: RelationTypeApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route('/{relationType<\d+>}', methods: [Request::METHOD_PATCH])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function update(
    RequestStudyArea $requestStudyArea,
    RelationType $relationType,
    Request $request,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $relationType);

    if ($relationType->isDeleted()) {
      throw $this->createNotFoundException();
    }

    $relationType = $this->getTypedFromBody($request, RelationTypeApiModel::class)
      ->mapToEntity($relationType);

    $this->getHandler()->update($relationType);

    return $this->createDataResponse(RelationTypeApiModel::fromEntity($relationType));
  }

  /** Delete an existing study area relation type. */
  #[OA\Response(response: 202, description: 'The relation type has been deleted')]
  #[Route('/{relationType<\d+>}', methods: [Request::METHOD_DELETE])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function delete(
    RequestStudyArea $requestStudyArea,
    RelationType $relationType): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $relationType);

    $this->getHandler()->delete($relationType, $this->getUser());

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): RelationTypeHandler
  {
    return new RelationTypeHandler($this->em, $this->validator, null);
  }
}
