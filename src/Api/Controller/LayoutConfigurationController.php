<?php

namespace App\Api\Controller;

use App\Api\Model\LayoutConfigurationApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\LayoutConfiguration;
use App\EntityHandler\LayoutConfigurationHandler;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Drenso\Shared\Http\AcceptedResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag('Layout Configuration')]
#[Route('/layoutconfiguration')]
class LayoutConfigurationController extends AbstractApiController
{
  /** Retrieve all study area layout configurations. */
  #[OA\Response(response: 200, description: 'All study area layout configurations', content: [
    new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: LayoutConfigurationApiModel::class))),
  ])]
  #[Route(methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(RequestStudyArea $requestStudyArea): JsonResponse
  {
    return $this->createDataResponse(
      $requestStudyArea->getStudyArea()->getLayoutConfigurations()
        ->map(fn (LayoutConfiguration $conf) => LayoutConfigurationApiModel::fromEntity($conf))
    );
  }

  /** Retrieve single study layout configuration. */
  #[OA\Response(response: 200, description: 'A single study area layout configuration', content: [new Model(type: LayoutConfigurationApiModel::class)])]
  #[Route('/{layoutConfiguration<\d+>}', methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function single(
    RequestStudyArea $requestStudyArea,
    LayoutConfiguration $layoutConfiguration): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);

    return $this->createDataResponse(LayoutConfigurationApiModel::fromEntity($layoutConfiguration));
  }

  /** Add a new study area layout configuration. */
  #[OA\RequestBody(description: 'The new layout configuration', required: true, content: [new Model(type: LayoutConfigurationApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The new layout configuration', content: [new Model(type: LayoutConfigurationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: [Request::METHOD_POST])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function add(
    RequestStudyArea $requestStudyArea,
    Request $request): JsonResponse
  {
    $layoutConfiguration = $this->getTypedFromBody($request, LayoutConfigurationApiModel::class)
      ->mapToEntity(null)
      ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($layoutConfiguration);

    // Ensure the response contains the completed entity
    $this->em->refresh($layoutConfiguration);

    return $this->createDataResponse(LayoutConfigurationApiModel::fromEntity($layoutConfiguration));
  }

  /** Update an existing study area layout configuration. */
  #[OA\RequestBody(description: 'The layout configuration properties to update', required: true, content: [new Model(type: LayoutConfigurationApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated layout configuration', content: [new Model(type: LayoutConfigurationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route('/{layoutConfiguration<\d+>}', methods: [Request::METHOD_PATCH])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function update(
    RequestStudyArea $requestStudyArea,
    LayoutConfiguration $layoutConfiguration,
    Request $request,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);

    $layoutConfiguration = $this->getTypedFromBody($request, LayoutConfigurationApiModel::class)
      ->mapToEntity($layoutConfiguration);

    $this->getHandler()->update($layoutConfiguration);

    return $this->createDataResponse(LayoutConfigurationApiModel::fromEntity($layoutConfiguration));
  }

  /** Delete an existing study area layout configuration. */
  #[OA\Response(response: 202, description: 'The layout configuration has been deleted')]
  #[Route('/{layoutConfiguration<\d+>}', methods: [Request::METHOD_DELETE])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function delete(
    RequestStudyArea $requestStudyArea,
    LayoutConfiguration $layoutConfiguration,
  ): AcceptedResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);

    $this->getHandler()->delete($layoutConfiguration);

    return new AcceptedResponse();
  }

  private function getHandler(): LayoutConfigurationHandler
  {
    return new LayoutConfigurationHandler($this->em, $this->validator, null);
  }
}
