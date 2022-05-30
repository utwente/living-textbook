<?php

namespace App\Api\Controller;

use App\Api\Model\ConceptApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\Concept;
use App\Entity\Tag;
use App\EntityHandler\ConceptEntityHandler;
use App\Repository\ConceptRepository;
use App\Repository\LearningPathRepository;
use App\Repository\TagRepository;
use App\Request\Wrapper\RequestStudyArea;
use Exception;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/concept") */
#[OA\Tag('Concept')]
class ConceptController extends AbstractApiController
{
  /**
   * Retrieve all study area concepts.
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area concepts', content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: ConceptApiModel::class))),
  ])]
  public function list(RequestStudyArea $requestStudyArea, ConceptRepository $conceptRepository): JsonResponse
  {
    return $this->createDataResponse(
        array_map(
            [ConceptApiModel::class, 'fromEntity'],
            $conceptRepository->findForStudyAreaOrderedByName($requestStudyArea->getStudyArea(), conceptsOnly: true)
        ),
        serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /**
   * Retrieve a single study area concept.
   *
   * @Route("/{concept<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'A single study area concept', content: [
      new Model(type: ConceptApiModel::class),
  ])]
  public function single(RequestStudyArea $requestStudyArea, Concept $concept): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    return $this->createDataResponse(
        ConceptApiModel::fromEntity($concept),
        serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /**
   * Add a new study area concept.
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new concept', required: true, content: [new Model(type: ConceptApiModel::class, groups: ['mutate', 'dotron'])])]
  #[OA\Response(response: 200, description: 'The new concept', content: [new Model(type: ConceptApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
      RequestStudyArea $requestStudyArea,
      Request $request): JsonResponse
  {
    $relationType = $this->getTypedFromBody($request, ConceptApiModel::class)
        ->mapToEntity(null)
        ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($relationType);

    return $this->createDataResponse(
        ConceptApiModel::fromEntity($relationType),
        serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /**
   * Update an existing study area concept.
   *
   * @Route("/{concept<\d+>}", methods={"PATCH"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The concept properties to update', required: true, content: [new Model(type: ConceptApiModel::class, groups: ['mutate', 'dotron'])])]
  #[OA\Response(response: 200, description: 'The updated concept', content: [new Model(type: ConceptApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function update(
      RequestStudyArea $requestStudyArea,
      Concept $concept,
      Request $request
  ): JsonResponse {
    $requestConcept = $this->getTypedFromBody($request, ConceptApiModel::class);

    $concept = $this->updateConcept(
        $requestStudyArea,
        $concept,
        $requestConcept
    );

    return $this->createDataResponse(
        ConceptApiModel::fromEntity($concept),
        serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /**
   * Update a batch of existing study area concepts.
   *
   * @Route("/batch", methods={"PATCH"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The concept properties to update', required: true, content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: ConceptApiModel::class, groups: ['mutate', 'dotron']))),
    ])]
  #[OA\Response(response: 200, description: 'The updated concepts', content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: ConceptApiModel::class))),
    ])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function batchUpdate(
      RequestStudyArea $requestStudyArea,
      Request $request,
      ConceptRepository $conceptRepository
  ): JsonResponse {
    $requestConcepts = $this->getArrayFromBody($request, ConceptApiModel::class);

    $this->em->beginTransaction();

    try {
      $concepts = array_map(fn ($requestConcept) => $this->updateConcept(
          $requestStudyArea,
          $conceptRepository->find($requestConcept->getId()),
          $requestConcept
      ), $requestConcepts);

      $this->em->commit();
    } catch (Exception $e) {
      $this->em->rollback();
      throw $e;
    }

    return $this->createDataResponse(
        array_map(fn ($concept) => ConceptApiModel::fromEntity($concept), $concepts),
        serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /**
   * Delete an existing study area concept.
   *
   * @Route("/{concept<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The concept has been deleted')]
  public function delete(
      RequestStudyArea $requestStudyArea,
      Concept $concept,
      LearningPathRepository $learningPathRepository): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    $this->getHandler()->delete($concept, $learningPathRepository);

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  /**
   * Add a tag to an existing study area concept.
   *
   * @Route("/{concept<\d+>}/tag", methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new tag id', required: true, content: [new OA\JsonContent(type: 'number')])]
  #[OA\Response(response: 200, description: 'The updated concept', content: [new Model(type: ConceptApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function addTag(
      Request $request,
      RequestStudyArea $requestStudyArea,
      Concept $concept,
      TagRepository $tagRepository): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $concept);
    /** @var int $requestTag */
    $requestTag = $this->getUntypedFromBody($request, 'int');

    $tag = $tagRepository->findOneBy(['id' => $requestTag, 'studyArea' => $requestStudyArea->getStudyArea()]);
    if (!$tag) {
      return $this->createBadRequestResponse(new ValidationFailedData('tag.not-found', []));
    }

    $this->getHandler()->update($concept->addTag($tag));

    return $this->createDataResponse(ConceptApiModel::fromEntity($concept));
  }

  /**
   * Replace the tags for an existing study area concept.
   *
   * @Route("/{concept<\d+>}/tag", methods={"PUT"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The tag ids', required: true, content: [new OA\JsonContent(type: 'array', items: new OA\Items(type: 'number'))])]
  #[OA\Response(response: 200, description: 'The updated concept', content: [new Model(type: ConceptApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function putTags(
      Request $request,
      RequestStudyArea $requestStudyArea,
      Concept $concept,
      TagRepository $tagRepository): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $concept);
    /** @var int[] $requestTag */
    $requestTags = $this->getUntypedFromBody($request, 'array<int>');

    $tags = $tagRepository->findForStudyAreaQb($requestStudyArea->getStudyArea())
        ->andWhere('t.id IN (:ids)')
        ->setParameter('ids', $requestTags)
        ->getQuery()->getResult();

    $conceptTags = $concept->getTags();
    $conceptTags->clear();
    foreach ($tags as $tag) {
      $conceptTags->add($tag);
    }
    $this->getHandler()->update($concept);

    return $this->createDataResponse(ConceptApiModel::fromEntity($concept));
  }

  /**
   * Remove an existing study area concept tag.
   *
   * @Route("/{concept<\d+>}/tag/{tag<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'The updated concept', content: [new Model(type: ConceptApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function deleteTag(
      RequestStudyArea $requestStudyArea,
      Concept $concept,
      Tag $tag): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $concept);
    $this->assertStudyAreaObject($requestStudyArea, $tag);

    $this->getHandler()->update($concept->removeTag($tag));

    return $this->createDataResponse(ConceptApiModel::fromEntity($concept));
  }

  private function getHandler(): ConceptEntityHandler
  {
    return new ConceptEntityHandler($this->em, $this->validator, null);
  }

  private function updateConcept(RequestStudyArea $requestStudyArea, Concept $concept, ConceptApiModel $requestConcept): Concept
  {
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    $concept = $requestConcept->mapToEntity($concept);

    $this->getHandler()->update($concept);

    return $concept;
  }
}
