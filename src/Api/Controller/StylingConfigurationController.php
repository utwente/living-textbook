<?php

namespace App\Api\Controller;

use App\Api\Model\StylingConfigurationApiModel;
use App\Api\Model\Validation\ValidationFailedData;
use App\Entity\StylingConfiguration;
use App\EntityHandler\StylingConfigurationHandler;
use App\Request\Wrapper\RequestStudyArea;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/** @Route("/stylingconfiguration") */
#[OA\Tag('Styling Configuration')]
class StylingConfigurationController extends AbstractApiController
{
  /**
   * Retrieve all study area styling configurations.
   *
   * @Route(methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'All study area styling configurations', content: [
      new OA\JsonContent(type: 'array', items: new OA\Items(new Model(type: StylingConfigurationApiModel::class))),
  ])]
  public function list(RequestStudyArea $requestStudyArea): JsonResponse
  {
    return $this->createDataResponse(
        $requestStudyArea->getStudyArea()->getStylingConfigurations()
            ->map(fn (StylingConfiguration $conf) => StylingConfigurationApiModel::fromEntity($conf))
    );
  }

  /**
   * Retrieve single study styling configuration.
   *
   * @Route("/{stylingConfiguration<\d+>}", methods={"GET"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   */
  #[OA\Response(response: 200, description: 'A single study area styling configuration', content: [new Model(type: StylingConfigurationApiModel::class)])]
  public function single(
      RequestStudyArea $requestStudyArea,
      StylingConfiguration $stylingConfiguration): JsonResponse
  {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);

    return $this->createDataResponse(StylingConfigurationApiModel::fromEntity($stylingConfiguration));
  }

  /**
   * Add a new study area styling configuration.
   *
   * @Route(methods={"POST"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The new styling configuration', required: true, content: [new Model(type: StylingConfigurationApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The new styling configuration', content: [new Model(type: StylingConfigurationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function add(
      RequestStudyArea $requestStudyArea,
      Request $request): JsonResponse
  {
    $relationType = $this->getTypedFromBody($request, StylingConfigurationApiModel::class)
        ->mapToEntity(null)
        ->setStudyArea($requestStudyArea->getStudyArea());

    $this->getHandler()->add($relationType);

    return $this->createDataResponse(StylingConfigurationApiModel::fromEntity($relationType));
  }

  /**
   * Update an existing study area styling configuration.
   *
   * @Route("/{stylingConfiguration<\d+>}", methods={"PATCH"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\RequestBody(description: 'The styling configuration properties to update', required: true, content: [new Model(type: StylingConfigurationApiModel::class, groups: ['mutate'])])]
  #[OA\Response(response: 200, description: 'The updated styling configuration', content: [new Model(type: StylingConfigurationApiModel::class)])]
  #[OA\Response(response: 400, description: 'Validation failed', content: [new Model(type: ValidationFailedData::class)])]
  public function update(
      RequestStudyArea $requestStudyArea,
      StylingConfiguration $stylingConfiguration,
      Request $request
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);

    $stylingConfiguration = $this->getTypedFromBody($request, StylingConfigurationApiModel::class)
        ->mapToEntity($stylingConfiguration);

    $this->getHandler()->update($stylingConfiguration);

    return $this->createDataResponse(StylingConfigurationApiModel::fromEntity($stylingConfiguration));
  }

  /**
   * Delete an existing study area styling configuration.
   *
   * @Route("/{stylingConfiguration<\d+>}", methods={"DELETE"})
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   */
  #[OA\Response(response: 202, description: 'The styling configuration has been deleted')]
  public function delete(
      RequestStudyArea $requestStudyArea,
      StylingConfiguration $stylingConfiguration
  ): JsonResponse {
    $this->assertStudyAreaObject($requestStudyArea, $stylingConfiguration);

    $this->getHandler()->delete($stylingConfiguration);

    return new JsonResponse(null, Response::HTTP_ACCEPTED);
  }

  private function getHandler(): StylingConfigurationHandler
  {
    return new StylingConfigurationHandler($this->em, $this->validator, null);
  }
}
