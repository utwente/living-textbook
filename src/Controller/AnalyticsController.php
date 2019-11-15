<?php

namespace App\Controller;

use App\Analytics\AnalyticsService;
use App\Analytics\Exception\VisualisationBuildFailed;
use App\Analytics\Exception\VisualisationDependenciesFailed;
use App\Analytics\Model\LearningPathVisualisationRequest;
use App\Form\Analytics\LearningPathAnalyticsType;
use App\Request\Wrapper\RequestStudyArea;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AnalyticsController
 *
 * @Route("/{_studyArea}/analytics", requirements={"_studyArea"="\d+"})
 */
class AnalyticsController extends AbstractController
{

  /**
   * The analytics dashboard
   *
   * @Route("/")
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @Template()
   *
   * @param RequestStudyArea $requestStudyArea
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
   * Generate the analytics
   *
   * @Route("/generate", methods={"POST"}, options={"expose"=true})
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request          $request
   * @param RequestStudyArea $requestStudyArea
   * @param AnalyticsService $analyticsService
   *
   * @return JsonResponse
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
}
