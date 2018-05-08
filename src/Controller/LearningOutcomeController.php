<?php

namespace App\Controller;

use App\Entity\LearningOutcome;
use App\Form\LearningOutcome\EditLearningOutcomeType;
use App\Form\Type\RemoveType;
use App\Repository\LearningOutcomeRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class LearningOutcomeController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/learningoutcome", requirements={"_studyArea"="\d+"})
 */
class LearningOutcomeController extends Controller
{

  /**
   * @Route("/add")
   * @Template
   *
   * @param Request                $request
   * @param RequestStudyArea       $studyArea
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function add(Request $request, RequestStudyArea $studyArea, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Create new object
    $learningOutcome = (new LearningOutcome())->setStudyArea($studyArea->getStudyArea());

    $form = $this->createForm(EditLearningOutcomeType::class, $learningOutcome);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($learningOutcome);
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('learning-outcome.saved', ['%item%' => $learningOutcome->getShortName()]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{learningOutcome}", requirements={"learningOutcome"="\d+"})
   * @Template()
   *
   * @param Request                $request
   * @param LearningOutcome        $learningOutcome
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function edit(Request $request, LearningOutcome $learningOutcome, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Create form and handle request
    $form = $this->createForm(EditLearningOutcomeType::class, $learningOutcome);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('learning-outcome.updated', ['%item%' => $learningOutcome->getShortName()]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    return [
        'learningOutcome' => $learningOutcome,
        'form'            => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   *
   * @param RequestStudyArea          $studyArea
   * @param LearningOutcomeRepository $repo
   *
   * @return array
   */
  public function list(RequestStudyArea $studyArea, LearningOutcomeRepository $repo)
  {
    return [
        'learningOutcomes' => $repo->findForStudyArea($studyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{learningOutcome}", requirements={"learningOutcome"="\d+"})
   * @Template()
   *
   * @param Request                $request
   * @param LearningOutcome        $learningOutcome
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function remove(Request $request, LearningOutcome $learningOutcome, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_learningoutcome_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->remove($learningOutcome);
      $em->flush();

      $this->addFlash('success', $trans->trans('learning-outcome.removed', ['%item%' => $learningOutcome->getShortName()]));

      return $this->redirectToRoute('app_learningoutcome_list');
    }

    return [
        'learningOutcome' => $learningOutcome,
        'form'            => $form->createView(),
    ];
  }

}
