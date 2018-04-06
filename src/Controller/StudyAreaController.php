<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Form\StudyArea\EditStudyAreaType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\ConceptRepository;
use App\Repository\StudyAreaRepository;
use Doctrine\ORM\EntityManagerInterface;
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
 * @Route("/studyarea")
 */
class StudyAreaController extends Controller
{
  /**
   * @Route("/add")
   * @Template()
   *
   * @param Request                $request
   * @param EntityManagerInterface $em
   *
   * @return array|Response
   */
  public function add(Request $request, EntityManagerInterface $em)
  {
    // Create new StudyArea
    $studyArea = new StudyArea();

    $form = $this->createForm(EditStudyAreaType::class, $studyArea, ['studyArea' => $studyArea]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $em->persist($studyArea);
      $em->flush();

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
   *
   * @param Request                $request
   * @param StudyArea              $studyArea
   * @param EntityManagerInterface $em
   *
   * @return Response|array
   */
  public function edit(Request $request, StudyArea $studyArea, EntityManagerInterface $em)
  {
    // Create form and handle request
    $form = $this->createForm(EditStudyAreaType::class, $studyArea, ['studyArea' => $studyArea]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $em->flush();

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
   *
   * @param EntityManagerInterface $em
   *
   * @return array
   */
  public function list(EntityManagerInterface $em)
  {
    $repo = $em->getRepository('App:StudyArea');
    assert($repo instanceof StudyAreaRepository);
    $studyAreas = $repo->findAll();

    return [
        'studyAreas' => $studyAreas,
    ];
  }

  /**
   * @Route("/remove/{studyArea}", requirements={"studyArea"="\d+"})
   * @Template()
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
        'studyArea'      => $studyArea,
        'form'           => $form->createView(),
    ];
  }

  /**
   * @Route("/{studyArea}", requirements={"studyArea"="\d+"}, options={"expose"=true})
   * @Template()
   *
   * @param StudyArea $studyArea
   *
   * @return array
   */
  public function show(StudyArea $studyArea, EntityManagerInterface $em)
  {
    $conceptRepo = $em->getRepository('App:Concept');
    assert($conceptRepo instanceof ConceptRepository);
    $concepts = $conceptRepo->findByStudyAreaOrderedByName($studyArea);

    return [
        'studyArea' => $studyArea,
        'concepts'  => $concepts,
    ];
  }

}
