<?php

namespace App\Api\Controller;

use App\Api\Model\StylingConfigurationConceptOverrideApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\Concept;
use App\Entity\StylingConfiguration;
use App\Entity\StylingConfigurationConceptOverride;
use App\EntityHandler\StylingConfigurationConceptOverrideHandler;
use App\Repository\StylingConfigurationConceptOverrideRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Drenso\Shared\Http\AcceptedResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[OA\Tag('Styling Configuration Override')]
#[Route('/stylingconfiguration/{stylingConfiguration<\d+>}/conceptoverride/{concept<\d+>}')]
class StylingConfigurationConceptOverrideController extends AbstractApiController
{
  /** Retrieve single concept style override. */
  #[OA\Response(response: 200, description: 'A single concept styling override', content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class)])]
  #[Route(methods: [Request::METHOD_GET])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function singleConcept(
    RequestStudyArea $requestStudyArea,
    StylingConfiguration $stylingConfiguration,
    Concept $concept,
    StylingConfigurationConceptOverrideRepository $overrideRepository,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $overrideRepository->getUnique($concept, $stylingConfiguration)) {
      throw $this->createNotFoundException();
    }

    return $this->createDataResponse(StylingConfigurationConceptOverrideApiModel::fromEntity($override));
  }

  /** Add a new concept styling override. */
  #[OA\RequestBody(description: 'The new override', required: true, content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class, groups: ['create'])])]
  #[OA\Response(response: 200, description: 'The new override', content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: [Request::METHOD_POST])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
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

  /** Update an existing concept styling override. */
  #[OA\RequestBody(description: 'The concept styling override to update', required: true, content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated override', content: [new Model(type: StylingConfigurationConceptOverrideApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  #[Route(methods: [Request::METHOD_PATCH])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function update(
    RequestStudyArea $requestStudyArea,
    StylingConfiguration $stylingConfiguration,
    Concept $concept,
    Request $request,
    StylingConfigurationConceptOverrideRepository $overrideRepository,
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $overrideRepository->getUnique($concept, $stylingConfiguration)) {
      throw $this->createNotFoundException();
    }

    $requestOverride = $this->getTypedFromBody($request, StylingConfigurationConceptOverrideApiModel::class);
    $override        = $requestOverride->mapToEntity($override);

    $this->getHandler()->update($override);

    return $this->createDataResponse(StylingConfigurationConceptOverrideApiModel::fromEntity($override));
  }

  /** Delete an existing concept styling override. */
  #[OA\Response(response: 202, description: 'The concept styling override has been deleted')]
  #[Route(methods: [Request::METHOD_DELETE])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function delete(
    RequestStudyArea $requestStudyArea,
    StylingConfiguration $stylingConfiguration,
    Concept $concept,
    StylingConfigurationConceptOverrideRepository $overrideRepository,
  ): AcceptedResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);
    $this->assertStudyAreaObject($requestStudyArea, $concept);

    if (!$override = $overrideRepository->getUnique($concept, $stylingConfiguration)) {
      throw $this->createNotFoundException();
    }

    $this->getHandler()->delete($override);

    return new AcceptedResponse();
  }

  private function getHandler(): StylingConfigurationConceptOverrideHandler
  {
    return new StylingConfigurationConceptOverrideHandler($this->em, $this->validator, null);
  }
}
