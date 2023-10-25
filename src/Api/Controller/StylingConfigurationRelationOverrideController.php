<?php

namespace App\Api\Controller;

use App\Api\Model\StylingConfigurationRelationOverrideApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\ConceptRelation;
use App\Entity\StylingConfiguration;
use App\Entity\StylingConfigurationRelationOverride;
use App\EntityHandler\StylingConfigurationRelationOverrideHandler;
use App\Repository\StylingConfigurationRelationOverrideRepository;
use App\Request\Wrapper\RequestStudyArea;
use Drenso\Shared\Http\AcceptedResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/stylingconfiguration/{stylingConfiguration<\d+>}/relationoverride/{relation<\d+>}") */
#[OA\Tag('Styling Configuration Override')]
class StylingConfigurationRelationOverrideController extends AbstractApiController
{
  /**
   * Retrieve single relation style override.
   *
   * @Route(methods={"GET"})
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'A single relation styling override', content: [new Model(type: StylingConfigurationRelationOverrideApiModel::class)])]
  public function singleRelation(
    RequestStudyArea $requestStudyArea,
    StylingConfiguration $stylingConfiguration,
    ConceptRelation $relation,
    StylingConfigurationRelationOverrideRepository $overrideRepository,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $relation->getSource() ?? $relation->getTarget());

    if (!$override = $overrideRepository->getUnique($relation, $stylingConfiguration)) {
      throw $this->createNotFoundException();
    }

    return $this->createDataResponse(StylingConfigurationRelationOverrideApiModel::fromEntity($override));
  }

  /**
   * Add a new relation styling override.
   *
   * @Route(methods={"POST"})
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new override', required: true, content: [new Model(type: StylingConfigurationRelationOverrideApiModel::class, groups: ['create'])])]
  #[OA\Response(response: 200, description: 'The new override', content: [new Model(type: StylingConfigurationRelationOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
    RequestStudyArea $requestStudyArea,
    StylingConfiguration $stylingConfiguration,
    ConceptRelation $relation,
    Request $request,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $relation->getSource() ?? $relation->getTarget());

    $requestOverride = $this->getTypedFromBody($request, StylingConfigurationRelationOverrideApiModel::class);

    $override = new StylingConfigurationRelationOverride(
      $requestStudyArea->getStudyArea(),
      $relation,
      $stylingConfiguration,
      $requestOverride->getOverride(),
    );

    $this->getHandler()->add($override);

    return $this->createDataResponse(StylingConfigurationRelationOverrideApiModel::fromEntity($override));
  }

  /**
   * Update an existing relation styling override.
   *
   * @Route(methods={"PATCH"})
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The relation styling override to update', required: true, content: [new Model(type: StylingConfigurationRelationOverrideApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated override', content: [new Model(type: StylingConfigurationRelationOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function update(
    RequestStudyArea $requestStudyArea,
    StylingConfiguration $stylingConfiguration,
    ConceptRelation $relation,
    Request $request,
    StylingConfigurationRelationOverrideRepository $overrideRepository,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $relation->getSource() ?? $relation->getTarget());

    if (!$override = $overrideRepository->getUnique($relation, $stylingConfiguration)) {
      throw $this->createNotFoundException();
    }

    $requestOverride = $this->getTypedFromBody($request, StylingConfigurationRelationOverrideApiModel::class);
    $override        = $requestOverride->mapToEntity($override);

    $this->getHandler()->update($override);

    return $this->createDataResponse(StylingConfigurationRelationOverrideApiModel::fromEntity($override));
  }

  /**
   * Delete an existing relation styling override.
   *
   * @Route(methods={"DELETE"})
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The relation styling override has been deleted')]
  public function delete(
    RequestStudyArea $requestStudyArea,
    StylingConfiguration $stylingConfiguration,
    ConceptRelation $relation,
    StylingConfigurationRelationOverrideRepository $overrideRepository,
  ): AcceptedResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $relation->getSource() ?? $relation->getTarget());

    if (!$override = $overrideRepository->getUnique($relation, $stylingConfiguration)) {
      throw $this->createNotFoundException();
    }

    $this->getHandler()->delete($override);

    return new AcceptedResponse();
  }

  private function getHandler(): StylingConfigurationRelationOverrideHandler
  {
    return new StylingConfigurationRelationOverrideHandler($this->em, $this->validator, null);
  }
}
