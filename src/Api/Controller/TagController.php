<?php

namespace App\Api\Controller;

use App\Api\Model\TagApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\Tag;
use App\EntityHandler\TagHandler;
use App\Repository\TagRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag('Tag')]
#[Route('/tag')]
class TagController extends AbstractApiController
{
  /** Retrieve all study area tags. */
  #[OA\Response(response: 200, description: 'All study area tags', content: [
    new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: TagApiModel::class))),
  ])]
  #[Route(methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(
    RequestStudyArea $requestStudyArea,
    TagRepository $tagRepository): JsonResponse
  {
    return $this->createDataResponse(array_map(
      [TagApiModel::class, 'fromEntity'],
      $tagRepository->findForStudyArea($requestStudyArea->getStudyArea())
    ));
  }

  /** Retrieve single study area tag. */
  #[OA\Response(response: 200, description: 'All study area tags', content: [new Model(type: TagApiModel::class)])]
  #[Route('/{tag<\d+>}', methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function single(
    RequestStudyArea $requestStudyArea,
    Tag $tag): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $tag);

    return $this->createDataResponse(TagApiModel::fromEntity($tag));
  }

  /** Add a new study area tag. */
  #[OA\RequestBody(description: 'The new tag', required: true, content: [
    new OA\JsonContent(ref: new Model(type: TagApiModel::class, groups: ['mutate'])),
  ])]
  #[OA\Response(response: 200, description: 'The new tag', content: [new Model(type: TagApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: [Request::METHOD_POST])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function add(
    RequestStudyArea $requestStudyArea,
    Request $request): JsonResponse
  {
    $tag = $this->getTypedFromBody($request, TagApiModel::class)
      ->mapToEntity(null)
      ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($tag);

    return $this->createDataResponse(TagApiModel::fromEntity($tag));
  }

  /** Update an existing study area tag. */
  #[OA\RequestBody(description: 'The tag properties to update', required: true, content: [
    new OA\JsonContent(ref: new Model(type: TagApiModel::class, groups: ['mutate'])),
  ])]
  #[OA\Response(response: 200, description: 'The updated tag', content: [new Model(type: TagApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route('/{tag<\d+>}', methods: [Request::METHOD_PATCH])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function update(
    RequestStudyArea $requestStudyArea,
    Tag $tag,
    Request $request): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $tag);

    $tag = $this->getTypedFromBody($request, TagApiModel::class)
      ->mapToEntity($tag);

    $this->getHandler()->update($tag);

    return $this->createDataResponse(TagApiModel::fromEntity($tag));
  }

  /** Delete an existing study area tag. */
  #[OA\Response(response: 202, description: 'The tag has been deleted')]
  #[Route('/{tag<\d+>}', methods: [Request::METHOD_DELETE])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function delete(
    RequestStudyArea $requestStudyArea,
    Tag $tag): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $tag);

    $this->getHandler()->delete($tag);

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): TagHandler
  {
    return new TagHandler($this->em, $this->validator, null);
  }
}
