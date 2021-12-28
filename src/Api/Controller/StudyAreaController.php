<?php

namespace App\Api\Controller;

use App\Api\Model\StudyArea;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @OA\Tag(name="Study area")
 */
class StudyAreaController extends AbstractApiController
{
  /**
   * Retrieve all API enabled study areas available for the current token
   *
   * @IsGranted("ROLE_USER")
   *
   * @OA\Response(
   *   response=200,
   *   description="The list of study areas available for the current token",
   *   @OA\JsonContent(
   *     type="array",
   *     @OA\Items(ref=@Model(type=StudyArea::class))
   *   )
   * )
   */
  public function list(StudyAreaRepository $studyAreaRepository): JsonResponse
  {
    return $this->createDataResponse(
        array_map(
            [StudyArea::class, 'fromEntity'],
            $studyAreaRepository
                ->getVisibleQueryBuilder($this->getUser())
                ->andWhere('sa.apiEnabled = TRUE')
                ->getQuery()->getResult()
        )
    );
  }

  /**
   * Retrieve information for a single study area
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @OA\Response(
   *   response=200,
   *   description="The study area information",
   *   @Model(type=StudyArea::class)
   * )
   */
  public function studyarea(RequestStudyArea $requestStudyArea): JsonResponse
  {
    return $this->createDataResponse(StudyArea::fromEntity($requestStudyArea->getStudyArea()));
  }
}
