<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
   * @IsGranted("ROLE_USER")
   *
   * @param RequestStudyArea $requestStudyArea
   * @param string           $pageUrl
   * @param RouterInterface  $router
   *
   * @return array|RedirectResponse
   */
  public function index(RequestStudyArea $requestStudyArea, string $pageUrl, RouterInterface $router)
  {
    // Disable profiler on the home page
    if ($this->has('profiler')) $this->get('profiler')->disable();

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
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea    $requestStudyArea
   * @param StudyAreaRepository $studyAreaRepository
   *
   * @return array
   *
   * @throws \Doctrine\ORM\NonUniqueResultException
   */
  public function dashboard(RequestStudyArea $requestStudyArea, StudyAreaRepository $studyAreaRepository)
  {
    $user = $this->getUser();
    $studyArea = $requestStudyArea->getStudyArea();

    if ($studyAreaRepository->getVisibleCount($user) > 1) {
      $form = $this->createFormBuilder()
          ->add('studyArea', EntityType::class, [
              'label'         => 'dashboard.study-area-switch',
              'class'         => StudyArea::class,
              'select2'       => true,
              'query_builder' => function (StudyAreaRepository $studyAreaRepository) use ($user, $studyArea) {
                return $studyAreaRepository->getVisibleQueryBuilder($user)
                    ->andWhere('sa != :current')
                    ->setParameter('current', $studyArea)
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
        'studyArea' => $studyArea,
    ];
  }
}
