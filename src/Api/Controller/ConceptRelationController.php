<?php

namespace App\Api\Controller;

use App\Api\Model\Create\CreateConceptRelationApiModel;
use App\Api\Model\Detailed\DetailedConceptRelationApiModel;
use App\Api\Model\Update\UpdateConceptRelationApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\ConceptRelation;
use App\EntityHandler\ConceptEntityHandler;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Drenso\Shared\IdMap\IdMap;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function array_map;
use function assert;

#[OA\Tag('Concept relation')]
#[Route('/conceptrelation')]
class ConceptRelationController extends AbstractApiController
{
  /** Retrieve all study area concept relations. */
  #[OA\Response(response: 200, description: 'All study area concept relations', content: [
    new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: DetailedConceptRelationApiModel::class))),
  ])]
  #[Route(methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(
    RequestStudyArea $requestStudyArea,
    ConceptRelationRepository $conceptRelationRepository): JsonResponse
  {
    return $this->createDataResponse(
      array_map(
        [DetailedConceptRelationApiModel::class, 'fromEntity'],
        $conceptRelationRepository->getByStudyArea($requestStudyArea->getStudyArea())
      ),
      serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /** Retrieve a single study area concept relation. */
  #[OA\Response(response: 200, description: 'Single study area concept relation')]
  #[Route('/{conceptRelation<\d+>}', methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function single(RequestStudyArea $requestStudyArea, ConceptRelation $conceptRelation): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource() ?? $conceptRelation->getTarget());

    return $this->createDataResponse(
      DetailedConceptRelationApiModel::fromEntity($conceptRelation),
      serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /** Add a new study area concept relation. */
  #[OA\RequestBody(description: 'The new concept relation', required: true, content: [
    new OA\JsonContent(ref: new Model(type: CreateConceptRelationApiModel::class)),
  ])]
  #[OA\Response(response: 200, description: 'The new concept relation', content: [new Model(type: DetailedConceptRelationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: [Request::METHOD_POST])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function add(
    RequestStudyArea $requestStudyArea,
    Request $request,
    ConceptRepository $conceptRepository,
    RelationTypeRepository $relationTypeRepository): JsonResponse
  {
    $studyArea       = $requestStudyArea->getStudyArea();
    $requestRelation = $this->getTypedFromBody($request, CreateConceptRelationApiModel::class);

    if (!$requestRelation->isValid()) {
      return $this->createBadRequestResponse(new ValidationFailedData('incomplete-object', []));
    }

    $source = $conceptRepository->findOneBy(['id' => $requestRelation->getSourceId(), 'studyArea' => $studyArea]);
    if (!$source) {
      return $this->createBadRequestResponse(new ValidationFailedData('source.not-found', []));
    }
    $target = $conceptRepository->findOneBy(['id' => $requestRelation->getTargetId(), 'studyArea' => $studyArea]);
    if (!$target) {
      return $this->createBadRequestResponse(new ValidationFailedData('target.not-found', []));
    }
    $relationType = $relationTypeRepository->findOneBy(['id' => $requestRelation->getRelationTypeId(), 'studyArea' => $studyArea]);
    if (!$relationType) {
      return $this->createBadRequestResponse(new ValidationFailedData('relation-type.not-found', []));
    }

    // Create the new relation
    $relation = new ConceptRelation()
      ->setSource($source)
      ->setTarget($target)
      ->setRelationType($relationType);

    $this->getHandler()->addRelation($relation);

    $this->em->flush();

    return $this->createDataResponse(DetailedConceptRelationApiModel::fromEntity($relation));
  }

  /** Update an existing study area concept relation. */
  #[OA\RequestBody(description: 'The concept relation to update', required: true, content: [
    new OA\JsonContent(ref: new Model(type: UpdateConceptRelationApiModel::class, groups: ['mutate', 'dotron'])),
  ])]
  #[OA\Response(response: 200, description: 'The updated concept relation', content: [new Model(type: DetailedConceptRelationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route('/{conceptRelation<\d+>}', methods: [Request::METHOD_PATCH])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function update(
    RequestStudyArea $requestStudyArea,
    Request $request,
    ConceptRelation $conceptRelation,
    RelationTypeRepository $relationTypeRepository): JsonResponse
  {
    $requestRelation = $this->getTypedFromBody($request, UpdateConceptRelationApiModel::class);

    $conceptRelation = $this->updateRelation(
      $requestStudyArea,
      $conceptRelation,
      $requestRelation,
      $relationTypeRepository
    );

    return $this->createDataResponse(
      DetailedConceptRelationApiModel::fromEntity($conceptRelation),
      serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea));
  }

  /** Update a batch of existing study area concept relations. */
  #[OA\RequestBody(description: 'The concept relations to update', required: true, content: [
    new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: UpdateConceptRelationApiModel::class, groups: ['mutate', 'dotron']))),
  ])]
  #[OA\Response(response: 200, description: 'The updated concept relations', content: [
    new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: DetailedConceptRelationApiModel::class))),
  ])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route('/batch', methods: [Request::METHOD_PATCH])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function batchUpdate(
    RequestStudyArea $requestStudyArea,
    Request $request,
    ConceptRelationRepository $relationRepository,
    RelationTypeRepository $relationTypeRepository,
  ): JsonResponse {
    $requestRelations = $this->getArrayFromBody($request, UpdateConceptRelationApiModel::class);

    $this->em->beginTransaction();

    try {
      // Prefetch all relations in a single query
      $dbRelations = new IdMap($relationRepository->findByIds(array_map(
        fn (UpdateConceptRelationApiModel $requestRelation) => $requestRelation->getId(),
        $requestRelations
      )));

      $conceptRelations = [];
      foreach ($requestRelations as $requestRelation) {
        if (!$dbRelation = $dbRelations[$requestRelation->getId()] ?? null) {
          // Silently ignore missing relation in database
          continue;
        }
        assert($dbRelation instanceof ConceptRelation);

        $conceptRelations[] = $this->updateRelation(
          $requestStudyArea,
          $dbRelation,
          $requestRelation,
          $relationTypeRepository
        );
      }

      $this->em->commit();
    } catch (Exception $e) {
      $this->em->rollback();
      throw $e;
    }

    return $this->createDataResponse(
      array_map(DetailedConceptRelationApiModel::fromEntity(...), $conceptRelations),
      serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea));
  }

  /** Delete an existing study area concept relation. */
  #[OA\Response(response: 202, description: 'The concept relation has been deleted')]
  #[Route('/{conceptRelation<\d+>}', methods: [Request::METHOD_DELETE])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function delete(RequestStudyArea $requestStudyArea, ConceptRelation $conceptRelation): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource());

    $this->getHandler()->deleteRelation($conceptRelation);

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): ConceptEntityHandler
  {
    return new ConceptEntityHandler($this->em, $this->validator, null);
  }

  private function updateRelation(
    RequestStudyArea $requestStudyArea,
    ConceptRelation $conceptRelation,
    UpdateConceptRelationApiModel $requestRelation,
    RelationTypeRepository $relationTypeRepository,
  ): ConceptRelation {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource());

    $relationType = $requestRelation->getRelationTypeId() ? $relationTypeRepository->findOneBy(['id' => $requestRelation->getRelationTypeId(), 'studyArea' => $requestStudyArea->getStudyArea()]) : null;

    $conceptRelation = $requestRelation->mapToEntity($conceptRelation, $relationType);

    $this->getHandler()->updateRelation($conceptRelation);

    return $conceptRelation;
  }
}
