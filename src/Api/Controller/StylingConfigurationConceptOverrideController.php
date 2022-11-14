<?php

namespace App\Api\Controller;

use App\Api\Model\StylingConfigurationConceptOverrideApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\Concept;
use App\Entity\StylingConfiguration;
use App\Entity\StylingConfigurationConceptOverride;
use App\EntityHandler\StylingConfigurationConceptOverrideHandler;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/stylingconfiguration/{stylingConfiguration<\d+>}/conceptoverride/{concept<\d+>}") */
#[OA\Tag('Styling Configuration Override')]
class StylingConfigurationConceptOverrideController extends AbstractApiController
{
  /**
   * Retrieve single concept style override.
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'A single concept styling override', content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class)])]
  public function singleConcept(
      RequestStudyArea $requestStudyArea,
      StylingConfiguration $stylingConfiguration,
      Concept $concept,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $stylingConfiguration->getConceptOverride($concept)) {
      throw $this->createNotFoundException();
    }

    return $this->createDataResponse(StylingConfigurationConceptOverrideApiModel::fromEntity($override));
  }

  /**
   * Add a new concept styling override.
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new override', required: true, content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class, groups: ['create'])])]
  #[OA\Response(response: 200, description: 'The new override', content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
      RequestStudyArea $requestStudyArea,
      StylingConfiguration $stylingConfiguration,
      Concept $concept,
      Request $request,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    $requestOverride = $this->getTypedFromBody($request, StylingConfigurationConceptOverrideApiModel::class);

    $override = new StylingConfigurationConceptOverride(
        $requestStudyArea->getStudyArea(),
        $concept,
        $stylingConfiguration,
        $requestOverride->getOverride(),
    );

    $this->getHandler()->add($override);

    return $this->createDataResponse(StylingConfigurationConceptOverrideApiModel::fromEntity($override));
  }

  /**
   * Update an existing concept styling override.
   *
   * @Route(methods={"PATCH"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The concept styling override to update', required: true, content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated override', content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function update(
      RequestStudyArea $requestStudyArea,
      StylingConfiguration $stylingConfiguration,
      Concept $concept,
      Request $request,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $stylingConfiguration->getConceptOverride($concept)) {
      throw $this->createNotFoundException();
    }

    $requestOverride = $this->getTypedFromBody($request, StylingConfigurationConceptOverrideApiModel::class);
    $requestOverride = $requestOverride->mapToEntity($override);

    $this->getHandler()->update($requestOverride);

    return $this->createDataResponse(StylingConfigurationConceptOverrideApiModel::fromEntity($requestOverride));
  }

  /**
   * Delete an existing concept styling override.
   *
   * @Route(methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The concept styling override has been deleted')]
  public function delete(
      RequestStudyArea $requestStudyArea,
      StylingConfiguration $stylingConfiguration,
      Concept $concept,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $stylingConfiguration->getConceptOverride($concept)) {
      throw $this->createNotFoundException();
    }

    $this->getHandler()->delete($override);

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): StylingConfigurationConceptOverrideHandler
  {
    return new StylingConfigurationConceptOverrideHandler($this->em, $this->validator, null);
  }
}
