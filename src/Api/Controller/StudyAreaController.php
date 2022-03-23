<?php

namespace App\Api\Controller;

use App\Api\Model\StudyAreaApiModel;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[OA\Tag('Study area')]
class StudyAreaController extends AbstractApiController
{
  /**
   * Retrieve all API enabled study areas available for the current token.
   *
   * @IsGranted("ROLE_USER")
   */
  #[OA\Response(response: 200, description: 'The list of study areas available for the current token', content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: StudyAreaApiModel::class))),
  ])]
  public function list(StudyAreaRepository $studyAreaRepository): JsonResponse
  {
    return $this->createDataResponse(
        array_map(
            [StudyAreaApiModel::class, 'fromEntity'],
            $studyAreaRepository
                ->getVisibleQueryBuilder($this->getUser())
                ->andWhere('sa.apiEnabled = TRUE')
                ->getQuery()->getResult()
        ),
        serializationGroups: ['Default', 'dotron']
    );
  }

  /**
   * Retrieve information for a single study area.
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'The study area information', content: [
      new Model(type: StudyAreaApiModel::class),
  ])]
  public function single(RequestStudyArea $requestStudyArea): JsonResponse
  {
    return $this->createDataResponse(
        StudyAreaApiModel::fromEntity($requestStudyArea->getStudyArea()),
        serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }
}
