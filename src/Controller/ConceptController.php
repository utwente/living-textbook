<?php

namespace App\Controller;

use App\Attribute\DenyOnFrozenStudyArea;
use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\PendingChange;
use App\Entity\User;
use App\EntityHandler\ConceptEntityHandler;
use App\Form\Concept\EditConceptType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\AnnotationRepository;
use App\Repository\ConceptRepository;
use App\Repository\LearningPathRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use App\Security\Voters\StudyAreaVoter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_studyArea<\d+>}/concept')]
class ConceptController extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly ReviewService $reviewService,
  ) {
  }

  #[Route('/add')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_concept_list', subject: 'requestStudyArea')]
  public function add(
    Request $request,
    RequestStudyArea $requestStudyArea,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new concept
    $concept  = (new Concept())->setStudyArea($studyArea);
    $snapshot = $this->reviewService->getSnapshot($concept);

    if ($request->query->has('instance')) {
      $concept->setInstance(true);
    }

    // Create form and handle request
    $form = $this->createForm(EditConceptType::class, $concept, [
      'concept' => $concept,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $this->getHandler()->add($concept, $snapshot);

      $this->addFlash('success', $trans->trans('concept.saved', ['%item%' => $concept->getName()]));

      // Check for forward to list
      if (!$concept->getId() || SaveType::isListClicked($form)) {
        return $this->redirectToRoute($concept->isInstance() ? 'app_concept_listinstances' : 'app_concept_list');
      }

      // Forward to show page
      return $this->redirectToRoute('app_concept_show', ['concept' => $concept->getId()]);
    }

    return $this->render('concept/add.html.twig', [
      'concept' => $concept,
      'form'    => $form,
    ]);
  }

  #[Route(path: '/edit/{concept<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_concept_show', routeParams: ['concept' => '{concept}'], subject: 'requestStudyArea')]
  public function edit(
    Request $request,
    RequestStudyArea $requestStudyArea,
    Concept $concept,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check study area
    if ($concept->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be edited
    if (!$this->reviewService->canObjectBeEdited($studyArea, $concept)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
        '%item%' => $trans->trans('concept._name'),
      ]));

      return $this->redirectToRoute($concept->isInstance() ? 'app_concept_listinstances' : 'app_concept_list');
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

    // Create snapshot
    $snapshot = $this->reviewService->getSnapshot($concept);

    // Create form and handle request
    $form = $this->createForm(EditConceptType::class, $concept, [
      'concept'             => $concept,
      'pending_change_info' => $this->reviewService->getPendingChangeObjectInformation($studyArea, $concept),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $this->getHandler()->update($concept, $snapshot, $originalOutgoingRelations, $originalIncomingRelations);

      $this->addFlash('success', $trans->trans('concept.updated', ['%item%' => $concept->getName()]));

      // Check for forward to list
      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute($concept->isInstance() ? 'app_concept_listinstances' : 'app_concept_list');
      }

      // Forward to show
      return $this->redirectToRoute('app_concept_show', ['concept' => $concept->getId()]);
    }

    return $this->render('concept/edit.html.twig', [
      'concept' => $concept,
      'form'    => $form,
    ]);
  }

  /**
   * Endpoint to re-edit the changes that are pending for submission. Fields that have already been submitted
   * in another pending change cannot be edited from here.
   */
  #[Route(path: '/edit/pending/{pendingChange}', requirements: ['pendingChange' => '\d+'])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_review_submit', subject: 'requestStudyArea')]
  public function editPending(
    Request $request,
    RequestStudyArea $requestStudyArea,
    PendingChange $pendingChange,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check study area
    if ($pendingChange->getStudyArea()->getId() != $studyArea->getId()
        || $pendingChange->getChangeType() === PendingChange::CHANGE_TYPE_REMOVE) {
      throw $this->createNotFoundException();
    }

    // We can either edit new concepts, or re-edit an existing concept
    if ($pendingChange->getChangeType() === PendingChange::CHANGE_TYPE_ADD) {
      $concept = (new Concept())
        ->setStudyArea($studyArea);
    } else {
      $concept = $this->reviewService->getOriginalObject($pendingChange);
    }

    // Verify it can be edited
    if (!$this->reviewService->canObjectBeEdited($studyArea, $concept)) {
      throw new NotFoundHttpException('When review has been disabled, pending edits can no longer be edited');
    }

    // Create snapshot. Do this of the current version, to ensure all changes are detected correctly
    $snapshot = $this->reviewService->getSnapshot($concept);

    // Retrieve matching review before em is cleared by the review service
    $review = $pendingChange->getReview();

    // Apply the pending change to the concept, take care to ignore the em changes
    $concept->applyChanges($pendingChange, $this->em, true);

    // Retrieve change information, take care to exclude the current pending changes
    $pendingChangeObjectInfo = $this->reviewService->getPendingChangeObjectInformation($studyArea, $concept, $pendingChange);

    // Create form and handle request
    $form = $this->createForm(EditConceptType::class, $concept, [
      'concept'              => $concept,
      'pending_change_info'  => $pendingChangeObjectInfo,
      'enable_save_and_list' => false,
      'cancel_route'         => $review ? 'app_review_showsubmission' : 'app_review_submit',
      'cancel_route_params'  => $review ? ['review' => $review->getId()] : [],
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $this->reviewService->updateChange($studyArea, $concept, $pendingChange, $snapshot);

      $this->addFlash('success', $trans->trans('concept.updated', ['%item%' => $concept->getName()]));

      if ($review) {
        return $this->redirectToRoute('app_review_showsubmission', ['review' => $review->getId()]);
      }

      return $this->redirectToRoute('app_review_submit');
    }

    if ($review) {
      $this->addFlash('review', $trans->trans('review.edit-pending-review'));
    } else {
      $this->addFlash('review', $trans->trans('review.edit-pending'));
    }

    return $this->render('concept/edit.html.twig', [
      'concept' => $concept,
      'form'    => $form,
    ]);
  }

  /** Instantiate an instance from a selected base concept. */
  #[Route('/instantiate/{concept<\d+>?null}', options: ['expose' => true])]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_concept_listinstances', subject: 'requestStudyArea')]
  public function instantiate(
    Request $request,
    RequestStudyArea $requestStudyArea,
    ?Concept $concept,
    RelationTypeRepository $relationRepository,
    TranslatorInterface $translator): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Block when in review mode
    if ($studyArea->isReviewModeEnabled()) {
      $this->addFlash('notice', $translator->trans('concept.instantiate.not-possible-review-enabled'));

      return $this->redirectToRoute('app_concept_listinstances');
    }

    // Check study area
    if ($concept && $concept->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Create the form
    $form = $this->createFormBuilder(['concept' => $concept])
      ->add('concept', EntityType::class, [
        'placeholder'   => 'dashboard.select-one',
        'required'      => true,
        'hide_label'    => true,
        'choice_label'  => 'name',
        'class'         => Concept::class,
        'select2'       => true,
        'query_builder' => fn (ConceptRepository $conceptRepository) => $conceptRepository->findForStudyAreaOrderByNameQb($studyArea, true),
        'constraints'   => [
          new NotNull(),
        ],
      ])
      ->add('submit', SaveType::class, [
        'save_label'           => 'concept.instantiate.instantiate',
        'save_icon'            => 'fa-code-fork',
        'enable_cancel'        => true,
        'cancel_route'         => 'app_concept_listinstances',
        'enable_save_and_list' => false,
        'attr'                 => [
          'class' => 'btn btn-outline-success',
        ],
      ])
      ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      // Time to create the instances
      /** @var Concept $baseConcept */
      $baseConcept          = $form->getData()['concept'];
      $instanceRelationType = $relationRepository->getOrCreateRelation(
        $studyArea, $translator->trans('concept.instantiate.default-relation-name'));

      $createInstance = fn (Concept $base): Concept => (new Concept())
        ->setStudyArea($studyArea)
        ->setInstance(true)
        ->setName($base->getName())
        ->addOutgoingRelation(
          (new ConceptRelation())
            ->setRelationType($instanceRelationType)
            ->setTarget($base)
        );

      $baseInstance = $createInstance($baseConcept);
      $this->em->persist($baseInstance);

      // Create instances for all first level relations
      foreach ($baseConcept->getIncomingRelations() as $incomingRelation) {
        $source = $incomingRelation->getSource();
        if ($source->isInstance()) {
          continue;
        }
        $instance = $createInstance($source);
        $this->em->persist($instance);

        $baseInstance->addIncomingRelation(
          (new ConceptRelation())
            ->setRelationType($incomingRelation->getRelationType())
            ->setSource($instance)
        );
      }
      foreach ($baseConcept->getOutgoingRelations() as $outgoingRelation) {
        $target = $outgoingRelation->getTarget();
        if ($target->isInstance()) {
          continue;
        }

        $instance = $createInstance($target);
        $this->em->persist($instance);

        $baseInstance->addOutgoingRelation(
          (new ConceptRelation())
            ->setRelationType($outgoingRelation->getRelationType())
            ->setTarget($instance)
        );
      }

      $this->em->flush();

      $this->addFlash('success', $translator->trans('concept.instantiate.success'));

      return $this->redirectToRoute('app_concept_listinstances');
    }

    return $this->render('concept/instantiate.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/list')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(
    ConceptRepository $repo,
    RequestStudyArea $requestStudyArea,
    AnnotationRepository $annotationRepository): Response
  {
    /** @var User $user */
    $user      = $this->getUser();
    $studyArea = $requestStudyArea->getStudyArea();

    $concepts         = $repo->findForStudyAreaOrderedByName($studyArea, false, true);
    $annotationCounts = $user
        ? $annotationRepository->getCountsForUserInStudyArea($user, $studyArea)
        : null;

    return $this->render('concept/list.html.twig', [
      'annotationCounts' => $annotationCounts,
      'studyArea'        => $studyArea,
      'concepts'         => $concepts,
    ]);
  }

  #[Route('/list/instances')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function listInstances(
    ConceptRepository $repo,
    RequestStudyArea $requestStudyArea,
    AnnotationRepository $annotationRepository): Response
  {
    /** @var User $user */
    $user      = $this->getUser();
    $studyArea = $requestStudyArea->getStudyArea();

    $concepts         = $repo->findForStudyAreaOrderedByName($studyArea, false, false, true);
    $annotationCounts = $user
        ? $annotationRepository->getCountsForUserInStudyArea($user, $studyArea)
        : null;

    return $this->render('concept/list.html.twig', [
      'instances'        => true,
      'annotationCounts' => $annotationCounts,
      'studyArea'        => $studyArea,
      'concepts'         => $concepts,
    ]);
  }

  #[Route(path: '/remove/{concept<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_concept_show', routeParams: ['concept' => '{concept}'], subject: 'requestStudyArea')]
  public function remove(
    Request $request,
    RequestStudyArea $requestStudyArea,
    Concept $concept,
    LearningPathRepository $learningPathRepository,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check study area
    if ($concept->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be deleted
    if (!$this->reviewService->canObjectBeRemoved($studyArea, $concept)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
        '%item%' => $trans->trans('concept._name'),
      ]));

      return $this->redirectToRoute($concept->isInstance() ? 'app_concept_listinstances' : 'app_concept_list');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route'        => 'app_concept_show',
      'cancel_route_params' => ['concept' => $concept->getId()],
    ]);
    $form->handleRequest($request);
    if (RemoveType::isRemoveClicked($form)) {
      // Save the data
      $this->getHandler()->delete($concept, $learningPathRepository);

      $this->addFlash('success', $trans->trans('concept.removed', ['%item%' => $concept->getName()]));

      return $this->redirectToRoute($concept->isInstance() ? 'app_concept_listinstances' : 'app_concept_list');
    }

    return $this->render('concept/remove.html.twig', [
      'concept'       => $concept,
      'learningPaths' => $learningPathRepository->findForConcept($concept),
      'form'          => $form,
    ]);
  }

  #[Route('/{concept<\d+>}', options: ['expose' => true])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function show(
    Concept $concept,
    RequestStudyArea $requestStudyArea,
    LearningPathRepository $learningPathRepository): Response
  {
    // Check study area
    if ($concept->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    return $this->render('concept/show.html.twig', [
      'concept'       => $concept,
      'learningPaths' => $learningPathRepository->findForConcept($concept),
    ]);
  }

  private function getHandler(): ConceptEntityHandler
  {
    // Double validation is not needed as we rely on the form validation
    return new ConceptEntityHandler($this->em, null, $this->reviewService);
  }
}
