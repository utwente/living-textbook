<?php

namespace App\Api\Controller;

use App\Api\Model\Tag;
use App\Api\Model\Validation\ValidationFailedData;
use App\EntityHandler\TagHandler;
use App\Repository\TagRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/tag") */
#[OA\Tag('Tag')]
class TagController extends AbstractApiController
{
  /**
   * Retrieve all study area tags.
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area tags', content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: Tag::class))),
  ])]
  public function list(
      RequestStudyArea $requestStudyArea,
      TagRepository $tagRepository): JsonResponse
  {
    return $this->createDataResponse(array_map(
        [Tag::class, 'fromEntity'],
        $tagRepository->findForStudyArea($requestStudyArea->getStudyArea())
    ));
  }

  /**
   * Retrieve single study area tag.
   *
   * @Route("/{tag<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area tags', content: [new Model(type: Tag::class)])]
  public function single(
      RequestStudyArea $requestStudyArea,
      \App\Entity\Tag $tag): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $tag);

    return $this->createDataResponse(Tag::fromEntity($tag));
  }

  /**
   * Add a new study area tag.
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new tag', required: true, content: [new Model(type: Tag::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The new tag', content: [new Model(type: Tag::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
      RequestStudyArea $requestStudyArea,
      Request $request): JsonResponse
  {
    $tag = $this->getTypedFromBody($request, Tag::class)
        ->mapToEntity(null)
        ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($tag);

    return $this->createDataResponse(Tag::fromEntity($tag));
  }

  /**
   * Update an existing study area tag.
   *
   * @Route("/{tag<\d+>}", methods={"PATCH"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The tag properties to update', required: true, content: [new Model(type: Tag::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated tag', content: [new Model(type: Tag::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function update(
      RequestStudyArea $requestStudyArea,
      \App\Entity\Tag $tag,
      Request $request): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $tag);

    $tag = $this->getTypedFromBody($request, Tag::class)
        ->mapToEntity($tag);

    $this->getHandler()->update($tag);

    return $this->createDataResponse(Tag::fromEntity($tag));
  }

  /**
   * Delete an existing study area tag.
   *
   * @Route("/{tag<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The tag has been deleted')]
  public function delete(
      RequestStudyArea $requestStudyArea,
      \App\Entity\Tag $tag): JsonResponse
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
