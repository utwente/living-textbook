<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

class DefaultController extends Controller
{

  /**
   * @Route("/", defaults={"_studyArea"=null, "pageUrl"=""})
   * @Route("/page/{_studyArea}/{pageUrl}", defaults={"_studyArea"=null, "pageUrl"=""}, requirements={"_studyArea"="\d+", "pageUrl"=".+"}, name="_home", options={"expose"=true})
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
    // @todo Check for amount of available study areas
    if (true) {
      $form = $this->createFormBuilder()
          ->add('studyArea', EntityType::class, [
              'label'         => 'dashboard.study-area-switch',
              'class'         => StudyArea::class,
              'select2'       => true,
              'query_builder' => function (StudyAreaRepository $studyAreaRepository) use ($requestStudyArea) {
                return $studyAreaRepository->createQueryBuilder('sa')
                    ->where('sa != :current')
                    ->setParameter('current', $requestStudyArea->getStudyArea())
                    ->orderBy('sa.name');
              },
          ])
          ->add('submit', SubmitType::class, [
              'label' => 'study-area.switch-to',
              'icon'  => 'fa-chevron-right',
              'attr'  => array(
                  'class'   => 'btn btn-outline-success',
                  'onclick' => 'eDispatch.pageLoad(Routing.generate(\'_home\', {\'_studyArea\': $(\'#form_studyArea\').val()}), {topLevel: true}); return false;',
              ),
          ])
          ->getForm();
    }

    return [
        'form'      => isset($form) ? $form->createView() : NULL,
        'studyArea' => $requestStudyArea->getStudyArea(),
    ];
  }
}
