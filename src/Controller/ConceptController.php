<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\Concept;
use App\Entity\PendingChange;
use App\Form\Concept\EditConceptType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\AnnotationRepository;
use App\Repository\ConceptRepository;
use App\Repository\LearningPathRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ConceptController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/concept", requirements={"_studyArea"="\d+"})
 */
class ConceptController extends AbstractController
{
  /**
   * @Route("/add")
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_concept_list", subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return array|Response
   */
  public function add(
      Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new concept
    $concept  = (new Concept())->setStudyArea($studyArea);
    $snapshot = $reviewService->getSnapshot($concept);

    // Create form and handle request
    $form = $this->createForm(EditConceptType::class, $concept, [
        'concept' => $concept,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $concept, PendingChange::CHANGE_TYPE_ADD, NULL, $snapshot);

      $this->addFlash('success', $trans->trans('concept.saved', ['%item%' => $concept->getName()]));

      // Check for forward to list
      if (!$concept->getId() || SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_concept_list');
      }

      // Forward to show page
      return $this->redirectToRoute('app_concept_show', ['concept' => $concept->getId()]);
    }

    return [
        'concept' => $concept,
        'form'    => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{concept}", requirements={"concept"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_concept_show", routeParams={"concept"="{concept}"}, subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Concept                $concept
   * @param ReviewService          $reviewService
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return Response|array
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, Concept $concept, ReviewService $reviewService,
      EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check study area
    if ($concept->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Map original relations
    $originalOutgoingRelations = new ArrayCollection();
    foreach ($concept->getOutgoingRelations() as $outgoingRelation) {
      $originalOutgoingRelations->add($outgoingRelation);
    }
    $originalIncomingRelations = new ArrayCollection();
    foreach ($concept->getIncomingRelations() as $incomingRelation) {
      $originalIncomingRelations->add($incomingRelation);
    }
    $snapshot = $reviewService->getSnapshot($concept);

    // Create form and handle request
    $form = $this->createForm(EditConceptType::class, $concept, [
        'concept' => $concept,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $concept, PendingChange::CHANGE_TYPE_EDIT,
          function () use (&$originalIncomingRelations, &$originalOutgoingRelations, &$em) {

            // Remove outdated relations
            foreach ($originalOutgoingRelations as $originalOutgoingRelation) {
              // Remove all original relations, because we just make new ones
              $em->remove($originalOutgoingRelation);
            }
            foreach ($originalIncomingRelations as $originalIncomingRelation) {
              // Remove all original relations, because we just make new ones
              $em->remove($originalIncomingRelation);
            }
          }, $snapshot);

      $this->addFlash('success', $trans->trans('concept.updated', ['%item%' => $concept->getName()]));

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_concept_list');
      }

      // Forward to show
      return $this->redirectToRoute('app_concept_show', ['concept' => $concept->getId()]);
    }

    return [
        'concept' => $concept,
        'form'    => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param ConceptRepository    $repo
   * @param RequestStudyArea     $requestStudyArea
   *
   * @param AnnotationRepository $annotationRepository
   *
   * @return array
   */
  public function list(ConceptRepository $repo, RequestStudyArea $requestStudyArea, AnnotationRepository $annotationRepository)
  {
    $studyArea        = $requestStudyArea->getStudyArea();
    $concepts         = $repo->findForStudyAreaOrderedByName($studyArea);
    $annotationCounts = $this->getUser()
        ? $annotationRepository->getCountsForUserInStudyArea($this->getUser(), $studyArea)
        : NULL;

    return [
        'annotationCounts' => $annotationCounts,
        'studyArea'        => $studyArea,
        'concepts'         => $concepts,
    ];
  }

  /**
   * @Route("/remove/{concept}", requirements={"concept"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_concept_show", routeParams={"concept"="{concept}"}, subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Concept                $concept
   * @param LearningPathRepository $learningPathRepository
   * @param ReviewService          $reviewService
   * @param TranslatorInterface    $trans
   *
   * @return array|RedirectResponse
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, Concept $concept,
      LearningPathRepository $learningPathRepository, ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check study area
    if ($concept->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route'        => 'app_concept_show',
        'cancel_route_params' => ['concept' => $concept->getId()],
    ]);
    $form->handleRequest($request);
    if (RemoveType::isRemoveClicked($form)) {
      $reviewService->storeChange($studyArea, $concept, PendingChange::CHANGE_TYPE_REMOVE,
          function (Concept $concept) use (&$learningPathRepository) {
            $learningPathRepository->removeElementBasedOnConcept($concept);
          });

      $this->addFlash('success', $trans->trans('concept.removed', ['%item%' => $concept->getName()]));

      return $this->redirectToRoute('app_concept_list');
    }

    return [
        'concept'       => $concept,
        'learningPaths' => $learningPathRepository->findForConcept($concept),
        'form'          => $form->createView(),
    ];
  }

  /**
   * @Route("/{concept}", requirements={"concept"="\d+"}, options={"expose"=true})
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Concept                $concept
   * @param RequestStudyArea       $requestStudyArea
   * @param LearningPathRepository $learningPathRepository
   *
   * @return array
   */
  public function show(Concept $concept, RequestStudyArea $requestStudyArea, LearningPathRepository $learningPathRepository)
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    return [
        'concept'       => $concept,
        'learningPaths' => $learningPathRepository->findForConcept($concept),
    ];
  }

}
