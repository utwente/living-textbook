<?php

namespace App\Api\Controller;

use App\Api\Model\Create\CreateConceptRelation;
use App\Api\Model\Detailed\DetailedConceptRelation;
use App\Api\Model\Validation\ValidationFailedData;
use App\EntityHandler\ConceptEntityHandler;
use App\EntityHandler\ConceptRelationEntityHandler;
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
  public function single(RequestStudyArea $requestStudyArea, \App\Entity\ConceptRelation $conceptRelation): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource() ?? $conceptRelation->getTarget());

    return $this->createDataResponse(DetailedConceptRelation::fromEntity($conceptRelation));
  }

  /**
   * Add a new study area concept relation
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(content: [new Model(type: CreateConceptRelation::class)])]
  #[OA\Response(response: 200, description: 'The new concept relation', content: [new Model(type: DetailedConceptRelation::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
      RequestStudyArea       $requestStudyArea,
      Request                $request,
      ConceptRepository      $conceptRepository,
      RelationTypeRepository $relationTypeRepository): JsonResponse
  {
    $requestRelation = $this->getTypedFromBody($request, CreateConceptRelation::class);

    if (!$requestRelation->isValid()) {
      return $this->createBadRequestResponse(new ValidationFailedData('incomplete-object', []));
    }

    $source = $conceptRepository->find($requestRelation->getSourceId());
    if ($source?->getStudyArea()->getId() !== $requestStudyArea->getStudyAreaId()) {
      return $this->createBadRequestResponse(new ValidationFailedData('source.not-found', []));
    }
    $target = $conceptRepository->find($requestRelation->getTargetId());
    if ($target?->getStudyArea()->getId() !== $requestStudyArea->getStudyAreaId()) {
      return $this->createBadRequestResponse(new ValidationFailedData('target.not-found', []));
    }
    $relationType = $relationTypeRepository->find($requestRelation->getRelationTypeId());
    if ($relationType?->getStudyArea()->getId() !== $requestStudyArea->getStudyAreaId()) {
      return $this->createBadRequestResponse(new ValidationFailedData('relation-type.not-found', []));
    }

    // Create the new relation
    $relation = (new \App\Entity\ConceptRelation())
        ->setSource($source)
        ->setTarget($target)
        ->setRelationType($relationType);

    $source->addOutgoingRelation($relation);
    $this->getConceptHandler()->update($source);

    $this->em->flush();

    return $this->createDataResponse(DetailedConceptRelation::fromEntity($relation));
  }

  /**
   * Delete an existing study area concept relation
   *
   * @Route("/{conceptRelation<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The concept relation has been deleted')]
  public function delete(RequestStudyArea $requestStudyArea, \App\Entity\ConceptRelation $conceptRelation): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $conceptRelation->getSource());

    $this->getHandler()->delete($conceptRelation);

    return new JsonResponse(NULL, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): ConceptRelationEntityHandler
  {
    return new ConceptRelationEntityHandler($this->em, $this->validator, NULL);
  }

  private function getConceptHandler(): ConceptEntityHandler
  {
    return new ConceptEntityHandler($this->em, $this->validator, NULL);
  }
}
