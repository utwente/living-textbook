<?php

namespace App\Api\Controller;

use App\Api\Model\StudyAreaApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\EntityHandler\StudyAreaEntityHandler;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

#[OA\Tag('Study area')]
class StudyAreaController extends AbstractApiController
{
  /**
   * Retrieve all API enabled study areas available for the current token.
   *
   * @IsGranted("ROLE_USER")
   */
  #[OA\Response(response: 200, description: 'The list of study areas available for the current token', content: [
    new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: StudyAreaApiModel::class, groups: ['Default', 'dotron']))),
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
    new Model(type: StudyAreaApiModel::class, groups: ['Default', 'dotron']),
  ])]
  public function single(RequestStudyArea $requestStudyArea): JsonResponse
  {
    return $this->createDataResponse(
      StudyAreaApiModel::fromEntity($requestStudyArea->getStudyArea()),
      serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  /**
   * Update an existing study area concept.
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The study area properties to update', required: true, content: [new Model(type: StudyAreaApiModel::class, groups: ['mutate', 'dotron'])])]
  #[OA\Response(response: 200, description: 'The updated study area', content: [new Model(type: StudyAreaApiModel::class, groups: ['Default', 'dotron'])])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function update(
    RequestStudyArea $requestStudyArea,
    Request $request
  ): JsonResponse {
    $studyArea = $requestStudyArea->getStudyArea();

    $studyArea = $this->getTypedFromBody($request, StudyAreaApiModel::class)
      ->mapToEntity($studyArea);

    $this->getHandler()->update($studyArea);

    return $this->createDataResponse(
      StudyAreaApiModel::fromEntity($studyArea),
      serializationGroups: $this->getDefaultSerializationGroup($requestStudyArea)
    );
  }

  private function getHandler(): StudyAreaEntityHandler
  {
    return new StudyAreaEntityHandler($this->em, $this->validator, null);
  }
}
