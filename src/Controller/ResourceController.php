<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Export\Provider\RdfProvider;
use App\Request\Wrapper\RequestStudyArea;
use EasyRdf\Exception;
use EasyRdf\Graph;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ResourceController.
 *
 * @Route("/resource/{_studyArea}", requirements={"_studyArea"="\d+"})
 */
class ResourceController extends AbstractController
{
  /**
   * @Route("/concept/{concept}", requirements={"concept"="\d+"}, options={"no_login_wrap"=true})
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @throws Exception
   */
  public function concept(RequestStudyArea $requestStudyArea, Concept $concept, RdfProvider $provider): JsonResponse
  {
    if ($concept->getStudyArea()->getId() !== $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
    $graph = new Graph($provider->generateConceptResourceUrl($concept));
    $provider->addConceptResource($concept, $graph);

    return $provider->exportGraph($graph);
  }

  /**
   * @Route("/learningpath/{learningPath}", requirements={"learningPath"="\d+"}, options={"no_login_wrap"=true})
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @throws Exception
   */
  public function learningPath(RequestStudyArea $requestStudyArea, LearningPath $learningPath, RdfProvider $provider): JsonResponse
  {
    if ($learningPath->getStudyArea()->getId() !== $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
    $graph = new Graph($provider->generateLearningPathResourceUrl($learningPath));
    $provider->addLearningPathResource($learningPath, $graph);

    return $provider->exportGraph($graph);
  }

  /**
   * @Route("/learningoutcome/{learningOutcome}", requirements={"learningOutcome"="\d+"},
   *                                              options={"no_login_wrap"=true})
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @throws Exception
   */
  public function learningOutcome(RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome, RdfProvider $provider): JsonResponse
  {
    if ($learningOutcome->getStudyArea()->getId() !== $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
    $graph = new Graph($provider->generateLearningOutcomeResourceUrl($learningOutcome));
    $provider->addLearningOutcomeResource($learningOutcome, $graph);

    return $provider->exportGraph($graph);
  }

  /**
   * @Route("/studyarea", options={"no_login_wrap"=true})
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @throws Exception
   */
  public function studyArea(RequestStudyArea $requestStudyArea, RdfProvider $provider): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    return $provider->exportStudyArea($studyArea);
  }
}
