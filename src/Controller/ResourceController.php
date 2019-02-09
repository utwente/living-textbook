<?php

namespace App\Controller;


use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\Export\Provider\RdfProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResourceController
 *
 * @Route("/resource")
 */
class ResourceController
{
  /**
   * @Route("/concept/{concept}", requirements={"concept"="\d+"})
   * @param Concept     $concept
   * @param RdfProvider $provider
   *
   * @return JsonResponse
   * @throws \EasyRdf_Exception
   */
  public function concept(Concept $concept, RdfProvider $provider): JsonResponse
  {
    $graph = new \EasyRdf_Graph($provider->generateConceptResourceUrl($concept));
    $provider->addConceptResource($concept, $graph);

    return $provider->exportGraph($graph);
  }

  /**
   * @Route("/learningpath/{learningPath}", requirements={"learningPath"="\d+"})
   * @param LearningPath $learningPath
   * @param RdfProvider  $provider
   *
   * @return JsonResponse
   * @throws \EasyRdf_Exception
   */
  public function learningPath(LearningPath $learningPath, RdfProvider $provider): JsonResponse
  {
    $graph = new \EasyRdf_Graph($provider->generateLearningPathResourceUrl($learningPath));
    $provider->addLearningPathResource($learningPath, $graph);

    return $provider->exportGraph($graph);
  }

  /**
   * @Route("/learningoutcome/{learningOutcome}", requirements={"learningOutcome"="\d+"})
   * @param LearningOutcome $learningOutcome
   * @param RdfProvider     $provider
   *
   * @return JsonResponse
   * @throws \EasyRdf_Exception
   */
  public function learningOutcome(LearningOutcome $learningOutcome, RdfProvider $provider): JsonResponse
  {
    $graph = new \EasyRdf_Graph($provider->generateLearningOutcomeResourceUrl($learningOutcome));
    $provider->addLearningOutcomeResource($learningOutcome, $graph);

    return $provider->exportGraph($graph);
  }

  /**
   * @Route("/studyarea/{studyArea}", requirements={"studyArea"="\d+"})
   * @param StudyArea   $studyArea
   * @param RdfProvider $provider
   *
   * @return Response
   * @throws \EasyRdf_Exception
   */
  public function studyArea(StudyArea $studyArea, RdfProvider $provider): Response
  {
    return $provider->exportStudyArea($studyArea);
  }
}