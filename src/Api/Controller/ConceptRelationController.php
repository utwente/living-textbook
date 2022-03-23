<?php

namespace App\Api\Controller;

use App\Api\Model\Create\CreateConceptRelationApiModel;
use App\Api\Model\Detailed\DetailedConceptRelationApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\EntityHandler\ConceptEntityHandler;
use App\Repository\ConceptRelationRepository;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/conceptrelation") */
#[OA\Tag('Concept relation')]
class ConceptRelationController extends AbstractApiController
{
  /**
   * Retrieve all study area concept relations.
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area concept relations', content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: DetailedConceptRelationApiModel::class))),
  ])]
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

  /**
   * Retrieve a single study area concept relation.
   *
   * @Route("/{conceptRelation<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'Single study area concept relation')]
  public function single(RequestStudyArea $requestStudyArea, \App\Entity\ConceptRelation $conceptRelation): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource() ?? $conceptRelation->getTarget());

    return $this->createDataResponse(
        DetailedConceptRelationApiModel::fromEntity($conceptRelation),
        serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /**
   * Add a new study area concept relation.
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new concept relation', required: true, content: [new Model(type: CreateConceptRelationApiModel::class)])]
  #[OA\Response(response: 200, description: 'The new concept relation', content: [new Model(type: DetailedConceptRelationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
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
    $relation = (new \App\Entity\ConceptRelation())
        ->setSource($source)
        ->setTarget($target)
        ->setRelationType($relationType);

    $this->getHandler()->addRelation($relation);

    $this->em->flush();

    return $this->createDataResponse(DetailedConceptRelationApiModel::fromEntity($relation));
  }

  /**
   * Delete an existing study area concept relation.
   *
   * @Route("/{conceptRelation<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The concept relation has been deleted')]
  public function delete(RequestStudyArea $requestStudyArea, \App\Entity\ConceptRelation $conceptRelation): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource());

    $this->getHandler()->deleteRelation($conceptRelation);

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): ConceptEntityHandler
  {
    return new ConceptEntityHandler($this->em, $this->validator, null);
  }
}
