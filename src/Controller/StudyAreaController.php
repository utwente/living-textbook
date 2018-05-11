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
   * @Template()
   * @IsGranted("ROLE_USER")
   *
   * @param Request                $request
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function add(Request $request, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Create new StudyArea
    $studyArea = new StudyArea();

    $form = $this->createForm(EditStudyAreaType::class, $studyArea, ['studyArea' => $studyArea]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $em->persist($studyArea);
      $em->flush();

      $this->addFlash('success', $trans->trans('study-area.saved', ['%item%' => $studyArea->getName()]));

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_studyarea_list');
      }

      // Forward to show page
      return $this->redirectToRoute('app_studyarea_show', ['studyArea' => $studyArea->getId()]);
    }

    return [
        'form' => $form->createView(),
    ];
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
    // Create form and handle request
    $form = $this->createForm(EditStudyAreaType::class, $studyArea, ['studyArea' => $studyArea]);
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
    $studyAreas = $studyAreaRepository->findAll();

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
   * @IsGranted("ROLE_USER")
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
