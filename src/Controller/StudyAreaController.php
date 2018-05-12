<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Form\StudyArea\EditStudyAreaType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\ConceptRepository;
use App\Repository\StudyAreaRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class StudyAreaController
 *
 * @author TobiasF
 *
 * @Route("/{_studyArea}/studyarea", requirements={"_studyArea"="\d+"})
 */
class StudyAreaController extends Controller
{
  /**
   * @Route("/add")
   * @Route("/add/first", defaults={"first" = true}, name="app_studyarea_add_first")
   * @Template()
   * @IsGranted("ROLE_USER")
   *
   * @param Request                $request
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   * @param bool                   $first
   *
   * @return array|Response
   */
  public function add(Request $request, EntityManagerInterface $em, TranslatorInterface $trans, $first = false)
  {
    // Create new StudyArea
    $studyArea = (new StudyArea())->setOwner($this->getUser());
    if ($first) $studyArea->setAccessType(StudyArea::ACCESS_INDIVIDUAL);

    $form = $this->createForm(EditStudyAreaType::class, $studyArea, [
        'studyArea'    => $studyArea,
        'select_owner' => false,
        'save_only'    => $first,
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $em->persist($studyArea);
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.saved', ['%item%' => $studyArea->getName()]));

      // Check for forward to home
      if ($first) {
        return $this->redirectToRoute('_home', ['_studyArea' => $studyArea->getId()]);
      }

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_studyarea_list');
      }

      // Forward to show page
      return $this->redirectToRoute('app_studyarea_show', ['studyArea' => $studyArea->getId()]);
    }

    $params = [
        'form' => $form->createView(),
    ];

    // Check for first
    if ($first) {
      return $this->render('study_area/add_first.html.twig', $params);
    }

    return $params;
  }

  /**
   * @Route("/edit/{studyArea}", requirements={"studyArea"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   *
   * @param Request                $request
   * @param StudyArea              $studyArea
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return Response|array
   */
  public function edit(Request $request, StudyArea $studyArea, EntityManagerInterface $em, TranslatorInterface $trans)
  {

    $this->isGranted("STUDYAREA_OWNER");

    // Create form and handle request
    $form = $this->createForm(EditStudyAreaType::class, $studyArea, ['studyArea' => $studyArea, 'select_owner' => false]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.updated', ['%item%' => $studyArea->getName()]));

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_studyarea_list');
      }

      // Forward to show
      return $this->redirectToRoute('app_studyarea_show', ['studyArea' => $studyArea->getId()]);
    }

    return [
        'studyArea' => $studyArea,
        'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("ROLE_USER")
   *
   * @param StudyAreaRepository $studyAreaRepository
   * @param RequestStudyArea    $requestStudyArea
   *
   * @return array
   */
  public function list(StudyAreaRepository $studyAreaRepository, RequestStudyArea $requestStudyArea)
  {
    $studyAreas = $studyAreaRepository->getVisible($this->getUser());

    return [
        'currentStudyArea' => $requestStudyArea->getStudyArea(),
        'studyAreas'       => $studyAreas,
    ];
  }

  /**
   * @Route("/remove/{studyArea}", requirements={"studyArea"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="studyArea")
   *
   * @param Request                $request
   * @param StudyArea              $studyArea
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|RedirectResponse
   */
  public function remove(Request $request, StudyArea $studyArea, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route'        => 'app_studyarea_show',
        'cancel_route_params' => ['studyArea' => $studyArea->getId()],
    ]);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em->remove($studyArea);
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.removed', ['%item%' => $studyArea->getName()]));

      return $this->redirectToRoute('app_studyarea_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/{studyArea}", requirements={"studyArea"="\d+"}, options={"expose"=true})
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="studyArea")
   *
   * @param StudyArea         $studyArea
   * @param ConceptRepository $conceptRepository
   *
   * @return array
   */
  public function show(StudyArea $studyArea, ConceptRepository $conceptRepository)
  {
    $concepts = $conceptRepository->findByStudyAreaOrderedByName($studyArea);

    return [
        'studyArea' => $studyArea,
        'concepts'  => $concepts,
    ];
  }

}
