<?php

namespace App\Api\Controller;

use App\Api\Model\Tag;
use App\Repository\TagRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/tag") */
#[OA\Tag('Tag')]
class TagController extends AbstractApiController
{
  /**
   * Retrieve all study area tags
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area tags', attachables: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: Tag::class))),
  ])]
  public function list(RequestStudyArea $requestStudyArea, TagRepository $tagRepository): JsonResponse
  {
    return $this->createDataResponse(array_map(
        [Tag::class, 'fromEntity'],
        $tagRepository->findForStudyArea($requestStudyArea->getStudyArea())
    ));
  }

  /**
   * Retrieve single study area tag
   *
   * @Route("/{tag<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area tags', attachables: [
      new Model(type: Tag::class),
  ])]
  public function single(RequestStudyArea $requestStudyArea, \App\Entity\Tag $tag): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $tag);

    return $this->createDataResponse(Tag::fromEntity($tag));
  }

  // todo: CRUD
}
