<?php

namespace App\Api\Controller;

use App\Api\Model\LayoutConfigurationOverrideApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\Concept;
use App\Entity\LayoutConfiguration;
use App\Entity\LayoutConfigurationOverride;
use App\EntityHandler\LayoutConfigurationOverrideHandler;
use App\Repository\LayoutConfigurationOverrideRepository;
use App\Request\Wrapper\RequestStudyArea;
use Drenso\Shared\Http\AcceptedResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag('Layout Configuration Override')]
#[Route(path: '/layoutconfiguration/{layoutConfiguration<\d+>}/override/{concept<\d+>}')]
class LayoutConfigurationOverrideController extends AbstractApiController
{
  /** Retrieve single layout override. */
  #[OA\Response(response: 200, description: 'A single layout override', content: [new Model(type: LayoutConfigurationOverrideApiModel::class)])]
  #[Route(methods: ['GET'])]
  #[IsGranted('STUDYAREA_SHOW', subject: 'requestStudyArea')]
  public function single(
    RequestStudyArea $requestStudyArea,
    LayoutConfiguration $layoutConfiguration,
    Concept $concept,
    LayoutConfigurationOverrideRepository $overrideRepository,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $overrideRepository->getUnique($concept, $layoutConfiguration)) {
      throw $this->createNotFoundException();
    }

    return $this->createDataResponse(LayoutConfigurationOverrideApiModel::fromEntity($override));
  }

  /** Add a new layout override. */
  #[OA\RequestBody(description: 'The new override', required: true, content: [new Model(type: LayoutConfigurationOverrideApiModel::class, groups: ['create'])])]
  #[OA\Response(response: 200, description: 'The new override', content: [new Model(type: LayoutConfigurationOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: ['POST'])]
  #[IsGranted('STUDYAREA_EDIT', subject: 'requestStudyArea')]
  public function add(
    RequestStudyArea $requestStudyArea,
    LayoutConfiguration $layoutConfiguration,
    Concept $concept,
    Request $request,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    $requestOverride = $this->getTypedFromBody($request, LayoutConfigurationOverrideApiModel::class);

    $override = new LayoutConfigurationOverride(
      $requestStudyArea->getStudyArea(),
      $concept,
      $layoutConfiguration,
      $requestOverride->getOverride(),
    );

    $this->getHandler()->add($override);

    return $this->createDataResponse(LayoutConfigurationOverrideApiModel::fromEntity($override));
  }

  /** Update an existing layout configuration override. */
  #[OA\RequestBody(description: 'The layout configuration override to update', required: true, content: [new Model(type: LayoutConfigurationOverrideApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated layout configuration override', content: [new Model(type: LayoutConfigurationOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: ['PATCH'])]
  #[IsGranted('STUDYAREA_EDIT', subject: 'requestStudyArea')]
  public function update(
    RequestStudyArea $requestStudyArea,
    LayoutConfiguration $layoutConfiguration,
    Concept $concept,
    Request $request,
    LayoutConfigurationOverrideRepository $overrideRepository,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $overrideRepository->getUnique($concept, $layoutConfiguration)) {
      throw $this->createNotFoundException();
    }

    $requestOverride = $this->getTypedFromBody($request, LayoutConfigurationOverrideApiModel::class)->mapToEntity($override);

    $this->getHandler()->update($requestOverride);

    return $this->createDataResponse(LayoutConfigurationOverrideApiModel::fromEntity($requestOverride));
  }

  /** Delete an existing layout configuration override. */
  #[OA\Response(response: 202, description: 'The layout override has been deleted')]
  #[Route(methods: ['DELETE'])]
  #[IsGranted('STUDYAREA_EDIT', subject: 'requestStudyArea')]
  public function delete(
    RequestStudyArea $requestStudyArea,
    LayoutConfiguration $layoutConfiguration,
    Concept $concept,
    LayoutConfigurationOverrideRepository $overrideRepository,
  ): AcceptedResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $overrideRepository->getUnique($concept, $layoutConfiguration)) {
      throw $this->createNotFoundException();
    }

    $this->getHandler()->delete($override);

    return new AcceptedResponse();
  }

  private function getHandler(): LayoutConfigurationOverrideHandler
  {
    return new LayoutConfigurationOverrideHandler($this->em, $this->validator, null);
  }
}
