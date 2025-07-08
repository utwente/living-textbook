<?php

namespace App\Controller;

use App\Analytics\AnalyticsService;
use App\Analytics\Exception\VisualisationBuildFailed;
use App\Analytics\Exception\VisualisationDependenciesFailed;
use App\Analytics\Model\LearningPathVisualisationRequest;
use App\Analytics\Model\SynthesizeRequest;
use App\Entity\User;
use App\Form\Analytics\LearningPathAnalyticsType;
use App\Form\Analytics\SynthesizeRequestType;
use App\Repository\LearningPathRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_key_exists;

#[Route('/{_studyArea<\d+>}/analytics')]
class AnalyticsController extends AbstractController
{
  /** The analytics dashboard. */
  #[Route('/')]
  #[IsGranted(StudyAreaVoter::ANALYTICS, subject: 'requestStudyArea')]
  public function dashboard(RequestStudyArea $requestStudyArea): Response
  {
    $form = $this->createForm(LearningPathAnalyticsType::class, new LearningPathVisualisationRequest(), [
      'study_area' => $requestStudyArea->getStudyArea(),
    ]);

    return $this->render('analytics/dashboard.html.twig', [
      'form' => $form,
    ]);
  }

  /**
   * Generate the analytics.
   *
   * @throws VisualisationBuildFailed
   * @throws VisualisationDependenciesFailed
   */
  #[Route('/generate', options: ['expose' => true], methods: [Request::METHOD_POST])]
  #[IsGranted(StudyAreaVoter::ANALYTICS, subject: 'requestStudyArea')]
  public function generate(
    Request $request, RequestStudyArea $requestStudyArea, AnalyticsService $analyticsService,
    SerializerInterface $serializer): JsonResponse
  {
    $data = new LearningPathVisualisationRequest();
    $form = $this->createForm(LearningPathAnalyticsType::class, $data, [
      'study_area' => $requestStudyArea->getStudyArea(),
    ]);
    $form->handleRequest($request);

    if (!$form->isSubmitted()) {
      throw new BadRequestHttpException('No data received');
    }

    if (!$form->isValid()) {
      $errors = [];
      foreach ($form->getErrors(true) as $error) {
        $key = $error->getOrigin()->getName();
        if (!array_key_exists($key, $errors)) {
          $errors[$key] = [];
        }
        $errors[$key][] = $error->getMessage();
      }

      return new JsonResponse($errors, 400);
    }

    $result = $analyticsService->buildForLearningPath($data);

    return JsonResponse::fromJsonString($serializer->serialize($result, 'json'));
  }

  #[Route(path: '/synthesize')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function synthesize(
    Request $request, RequestStudyArea $requestStudyArea, AnalyticsService $analyticsService,
    TranslatorInterface $translator, LearningPathRepository $learningPathRepository): Response
  {
    if ($learningPathRepository->getCountForStudyArea($requestStudyArea->getStudyArea()) === 0) {
      $this->addFlash('error', $translator->trans('analytics.synthesize-not-possible'));

      return $this->redirectToRoute('app_analytics_dashboard');
    }

    $synthRequest = new SynthesizeRequest($requestStudyArea->getStudyArea());
    $form         = $this->createForm(SynthesizeRequestType::class, $synthRequest);

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $analyticsService->synthesizeDataForStudyArea($requestStudyArea->getStudyArea(), $synthRequest);

      $this->addFlash('success', $translator->trans('analytics.synthesize-success'));

      return $this->redirectToRoute('app_analytics_dashboard');
    }

    return $this->render('analytics/synthesize.html.twig', [
      'form' => $form,
    ]);
  }
}
