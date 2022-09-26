<?php

namespace App\Api\Controller;

use App\Api\Model\LayoutConfigurationApiModel;
use App\Api\Model\LayoutConfigurationOverrideApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\LayoutConfiguration;
use App\Entity\LayoutConfigurationOverride;
use App\EntityHandler\LayoutConfigurationHandler;
use App\Repository\ConceptRepository;
use App\Request\Wrapper\RequestStudyArea;
use Exception;
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
        ->mapToEntity(null, null)
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
      Request $request,
      ConceptRepository $conceptRepository,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $layoutConfiguration);

    $requestLayoutConfiguration = $this->getTypedFromBody($request, LayoutConfigurationApiModel::class);

    $requestOverrides = $requestLayoutConfiguration->getOverrides();

    $this->em->beginTransaction();

    try {
      $this->updateOverrides(
          $requestOverrides,
          $conceptRepository,
          $layoutConfiguration,
      );

      $layoutConfiguration = $requestLayoutConfiguration->mapToEntity($layoutConfiguration);

      $this->getHandler()->update($layoutConfiguration);

      $this->em->commit();
    } catch (Exception $e) {
      $this->em->rollback();
      throw $e;
    }

    $this->em->refresh($layoutConfiguration);

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

  /**
   * @param LayoutConfigurationApiModel[] $requestOverrides
   */
  public function updateOverrides(
      array $requestOverrides,
      ConceptRepository $conceptRepository,
      LayoutConfiguration $layoutConfiguration,
  ): void {
    // Split into new and existing overrides
    $requestNewOverrides    = [];
    $requestUpdateOverrides = [];

    /** @var LayoutConfigurationOverrideApiModel $override */
    foreach ($requestOverrides as $override) {
      if ($override->getId()) {
        $requestUpdateOverrides[] = $override;
      } else {
        $requestNewOverrides[] = $override;
      }
    }

    // Split into updates and removals
    $existingOverrides = $layoutConfiguration->getOverrides()->toArray();

    $requestIds  = array_map(fn (LayoutConfigurationOverrideApiModel $override) => $override->getId(), $requestUpdateOverrides);
    $existingIds = array_map(fn (LayoutConfigurationOverride $override) => $override->getId(), $existingOverrides);

    $updateIds = array_intersect($requestIds, $existingIds);
    $removeIds = array_diff($existingIds, $updateIds);

    // Remove overrides
    $removeOverrides = $layoutConfiguration->getOverrides()->filter(fn (LayoutConfigurationOverride $override) => in_array($override->getId(), $removeIds));
    /** @var LayoutConfigurationOverride $override */
    foreach ($removeOverrides as $override) {
      if (!$override->isDeleted()) {
        $this->em->remove($override);
      }
    }
    $this->em->flush();

    // Update overrides
    $updateExistingOverrides = array_filter($existingOverrides, fn (LayoutConfigurationOverride $override) => in_array($override->getId(), $updateIds));
    $updateRequestOverrides  = array_filter($requestOverrides, fn (LayoutConfigurationOverrideApiModel $override) => in_array($override->getId(), $updateIds));

    assert(count($updateExistingOverrides) == count($updateRequestOverrides));

    // Sort so the ids are matched
    usort($updateExistingOverrides, fn ($a, $b) => $a->getId() - $b->getId());
    usort($updateRequestOverrides, fn ($a, $b) => $a->getId() - $b->getId());

    array_map(
        fn (LayoutConfigurationOverrideApiModel $apiOverride,
            LayoutConfigurationOverride $existingOverride) => $apiOverride->mapToEntity($existingOverride),
        $updateRequestOverrides,
        $updateExistingOverrides
    );

    // Add new overrides
    $studyArea = $layoutConfiguration->getStudyArea();
    array_map(function (LayoutConfigurationOverrideApiModel $override) use (
        $layoutConfiguration,
        $conceptRepository,
        $studyArea
    ) {
      $newOverride = new LayoutConfigurationOverride(
          $studyArea,
          $conceptRepository->findForStudyArea($studyArea, $override->getConcept()),
          $layoutConfiguration,
          $override->getOverride(),
      );
      $this->em->persist($newOverride);
    }, $requestNewOverrides);
    $this->em->flush();
  }

  private function getHandler(): LayoutConfigurationHandler
  {
    return new LayoutConfigurationHandler($this->em, $this->validator, null);
  }
}
