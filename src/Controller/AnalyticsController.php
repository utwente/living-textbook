<?php

namespace App\Controller;

use App\Analytics\AnalyticsService;
use App\Analytics\Exception\VisualisationBuildFailed;
use App\Analytics\Exception\VisualisationDependenciesFailed;
use App\Analytics\Model\LearningPathVisualisationRequest;
use App\Analytics\Model\SynthesizeRequest;
use App\Form\Analytics\LearningPathAnalyticsType;
use App\Form\Analytics\SynthesizeRequestType;
use App\Repository\LearningPathRepository;
use App\Request\Wrapper\RequestStudyArea;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AnalyticsController.
 *
 * @Route("/{_studyArea}/analytics", requirements={"_studyArea"="\d+"})
 */
class AnalyticsController extends AbstractController
{
  /**
   * The analytics dashboard.
   *
   * @Route("/")
   * @IsGranted("STUDYAREA_ANALYTICS", subject="requestStudyArea")
   * @Template()
   *
   * @return array
   */
  public function dashboard(RequestStudyArea $requestStudyArea)
  {
    $form = $this->createForm(LearningPathAnalyticsType::class, new LearningPathVisualisationRequest(), [
        'study_area' => $requestStudyArea->getStudyArea(),
    ]);

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * Generate the analytics.
   *
   * @Route("/generate", methods={"POST"}, options={"expose"=true})
   * @IsGranted("STUDYAREA_ANALYTICS", subject="requestStudyArea")
   *
   * @throws VisualisationBuildFailed
   * @throws VisualisationDependenciesFailed
   */
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

  /**
   * @Route("/synthesize")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @Template
   */
  public function synthesize(
      Request $request, RequestStudyArea $requestStudyArea, AnalyticsService $analyticsService,
      TranslatorInterface $translator, LearningPathRepository $learningPathRepository)
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

    return [
        'form' => $form->createView(),
    ];
  }
}
