<?php

namespace App\Api\Controller;

use App\Api\Model\LayoutConfigurationApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\LayoutConfiguration;
use App\EntityHandler\LayoutConfigurationHandler;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/layoutconfiguration") */
#[OA\Tag('Layout Configuration')]
class LayoutConfigurationController extends AbstractApiController
{
  /**
   * Retrieve all study area layout configurations.
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area layout configurations', content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: LayoutConfigurationApiModel::class))),
  ])]
  public function list(RequestStudyArea $requestStudyArea): JsonResponse
  {
    return $this->createDataResponse(
        $requestStudyArea->getStudyArea()->getLayoutConfigurations()
            ->map(fn (LayoutConfiguration $conf) => LayoutConfigurationApiModel::fromEntity($conf))
    );
  }

  /**
   * Retrieve single study layout configuration.
   *
   * @Route("/{layoutConfiguration<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'A single study area layout configuration', content: [new Model(type: LayoutConfigurationApiModel::class)])]
  public function single(
      RequestStudyArea $requestStudyArea,
      LayoutConfiguration $layoutConfiguration): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);

    return $this->createDataResponse(LayoutConfigurationApiModel::fromEntity($layoutConfiguration));
  }

  /**
   * Add a new study area layout configuration.
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new layout configuration', required: true, content: [new Model(type: LayoutConfigurationApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The new layout configuration', content: [new Model(type: LayoutConfigurationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
      RequestStudyArea $requestStudyArea,
      Request $request): JsonResponse
  {
    $relationType = $this->getTypedFromBody($request, LayoutConfigurationApiModel::class)
        ->mapToEntity(null)
        ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($relationType);

    return $this->createDataResponse(LayoutConfigurationApiModel::fromEntity($relationType));
  }

  /**
   * Update an existing study area layout configuration.
   *
   * @Route("/{layoutConfiguration<\d+>}", methods={"PATCH"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The layout configuration properties to update', required: true, content: [new Model(type: LayoutConfigurationApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated layout configuration', content: [new Model(type: LayoutConfigurationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function update(
      RequestStudyArea $requestStudyArea,
      LayoutConfiguration $layoutConfiguration,
      Request $request
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);

    $layoutConfiguration = $this->getTypedFromBody($request, LayoutConfigurationApiModel::class)
        ->mapToEntity($layoutConfiguration);

    $this->getHandler()->update($layoutConfiguration);

    return $this->createDataResponse(LayoutConfigurationApiModel::fromEntity($layoutConfiguration));
  }

  /**
   * Delete an existing study area layout configuration.
   *
   * @Route("/{layoutConfiguration<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The layout configuration has been deleted')]
  public function delete(
      RequestStudyArea $requestStudyArea,
      LayoutConfiguration $layoutConfiguration
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);

    $this->getHandler()->delete($layoutConfiguration);

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): LayoutConfigurationHandler
  {
    return new LayoutConfigurationHandler($this->em, $this->validator, null);
  }
}
