<?php

namespace App\Controller;

use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class DefaultController extends Controller
{

  /**
   * @Route("/", defaults={"_studyArea"=null, "pageUrl"=""})
   * @Route("/page/{_studyArea}/{pageUrl}", defaults={"_studyArea"=null, "pageUrl"=""}, requirements={"_studyArea"="\d+", "pageUrl"=".+"}, name="_home")
   * @Route("/page/{pageUrl}", defaults={"_studyArea"=null, "pageUrl"=""}, requirements={"pageUrl"=".+"})
   * @Template("double_column.html.twig")
   *
   * @param RequestStudyArea    $requestStudyArea
   * @param string              $pageUrl
   * @param RouterInterface     $router
   * @param StudyAreaRepository $studyAreaRepository
   *
   * @return array|RedirectResponse
   */
  public function index(RequestStudyArea $requestStudyArea, string $pageUrl, RouterInterface $router, StudyAreaRepository $studyAreaRepository)
  {
    // Disable profiler on the home page
    if ($this->get('profiler')) $this->get('profiler')->disable();

    // Retrieve actual study area from wrapper
    $studyArea = $requestStudyArea->getStudyArea();

    return [
        'studyArea' => $studyArea,
        'pageUrl'   => $pageUrl != ''
            ? '/' . $studyArea->getId() . '/' . $pageUrl
            : $router->generate('app_default_dashboard', ['_studyArea' => $studyArea->getId()]),
    ];
  }

  /**
   * @Route("/{_studyArea}/dashboard", requirements={"_studyArea"="\d+"})
   * @Template
   *
   * @param RequestStudyArea $requestStudyArea
   *
   * @return array
   */
  public function dashboard(RequestStudyArea $requestStudyArea)
  {
    return [
        'studyArea' => $requestStudyArea->getStudyArea()
    ];
  }
}
