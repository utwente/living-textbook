<?php

namespace App\Controller;

use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\LearningPath;
use App\Export\Provider\RdfProvider;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use EasyRdf\Exception;
use EasyRdf\Graph;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/resource/{_studyArea<\d+>}')]
class ResourceController extends AbstractController
{
  /** @throws Exception */
  #[Route('/concept/{concept<\d+>}', options: ['no_login_wrap' => true])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function concept(RequestStudyArea $requestStudyArea, Concept $concept, RdfProvider $provider): JsonResponse
  {
    if ($concept->getStudyArea()->getId() !== $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
    $graph = new Graph($provider->generateConceptResourceUrl($concept));
    $provider->addConceptResource($concept, $graph);

    return $provider->exportGraph($graph);
  }

  /** @throws Exception */
  #[Route('/learningpath/{learningPath<\d+>}', options: ['no_login_wrap' => true])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function learningPath(RequestStudyArea $requestStudyArea, LearningPath $learningPath, RdfProvider $provider): JsonResponse
  {
    if ($learningPath->getStudyArea()->getId() !== $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
    $graph = new Graph($provider->generateLearningPathResourceUrl($learningPath));
    $provider->addLearningPathResource($learningPath, $graph);

    return $provider->exportGraph($graph);
  }

  /** @throws Exception */
  #[Route('/learningoutcome/{learningOutcome<\d+>}', options: ['no_login_wrap' => true])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function learningOutcome(RequestStudyArea $requestStudyArea, LearningOutcome $learningOutcome, RdfProvider $provider): JsonResponse
  {
    if ($learningOutcome->getStudyArea()->getId() !== $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
    $graph = new Graph($provider->generateLearningOutcomeResourceUrl($learningOutcome));
    $provider->addLearningOutcomeResource($learningOutcome, $graph);

    return $provider->exportGraph($graph);
  }

  /** @throws Exception */
  #[Route('/studyarea', options: ['no_login_wrap' => true])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function studyArea(RequestStudyArea $requestStudyArea, RdfProvider $provider): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    return $provider->exportStudyArea($studyArea);
  }
}
