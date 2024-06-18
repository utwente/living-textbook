<?php

namespace App\Controller;

use App\Attribute\DenyOnFrozenStudyArea;
use App\Entity\RelationType;
use App\EntityHandler\RelationTypeHandler;
use App\Form\RelationType\EditRelationTypeType;
use App\Form\Type\RemoveType;
use App\Repository\ConceptRelationRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use App\Security\Voters\StudyAreaVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_studyArea<\d+>}/relationtype')]
class RelationTypeController extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em,
    private readonly ReviewService $reviewService,
  ) {
  }

  #[Route('/add')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_relationtype_list', subject: 'requestStudyArea')]
  public function add(
    Request $request,
    RequestStudyArea $requestStudyArea,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new
    $relationType = (new RelationType())->setStudyArea($studyArea);
    $snapshot     = $this->reviewService->getSnapshot($relationType);

    $form = $this->createForm(EditRelationTypeType::class, $relationType);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $this->getHandler()->add($relationType, $snapshot);

      $this->addFlash('success', $trans->trans('relation-type.saved', ['%item%' => $relationType->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_relationtype_list');
    }

    return $this->render('relation_type/add.html.twig', [
      'relationType' => $relationType,
      'form'         => $form,
    ]);
  }

  /** @noRector Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector */
  #[Route('/edit/{relationType<\d+>}')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_relationtype_list', subject: 'requestStudyArea')]
  public function edit(
    Request $request,
    RequestStudyArea $requestStudyArea,
    RelationType $relationType,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($relationType->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Check if not removed
    if ($relationType->getDeletedAt() !== null) {
      throw $this->createNotFoundException();
    }

    // Verify it can be edited
    if (!$this->reviewService->canObjectBeEdited($studyArea, $relationType)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
        '%item%' => $trans->trans('relation-type._name'),
      ]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    // Create snapshot
    $snapshot = $this->reviewService->getSnapshot($relationType);

    // Create form and handle request
    $form = $this->createForm(EditRelationTypeType::class, $relationType, [
      'pending_change_info' => $this->reviewService->getPendingChangeObjectInformation($studyArea, $relationType),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $this->getHandler()->update($relationType, $snapshot);

      $this->addFlash('success', $trans->trans('relation-type.updated', ['%item%' => $relationType->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_relationtype_list');
    }

    return $this->render('relation_type/edit.html.twig', [
      'relationType' => $relationType,
      'form'         => $form->createView(),
    ]);
  }

  #[Route('/list')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(RequestStudyArea $requestStudyArea, RelationTypeRepository $repo): Response
  {
    return $this->render('relation_type/list.html.twig', [
      'studyArea'     => $requestStudyArea->getStudyArea(),
      'relationTypes' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ]);
  }

  /** @noRector Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector */
  #[Route(path: '/remove/{relationType<\d+>}', requirements: ['relationType' => '\d+'])]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_relationtype_list', subject: 'requestStudyArea')]
  public function remove(
    Request $request,
    RequestStudyArea $requestStudyArea,
    RelationType $relationType,
    ConceptRelationRepository $conceptRelationRepository,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($relationType->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Check if not already deleted
    if ($relationType->getDeletedAt() !== null) {
      $this->addFlash('warning', $trans->trans('relation-type.removed-already', ['%item%' => $relationType->getName()]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    // Verify it can be deleted
    if (!$this->reviewService->canObjectBeRemoved($studyArea, $relationType)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
        '%item%' => $trans->trans('relation-type._name'),
      ]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_relationtype_list',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      // Save the data
      $this->getHandler()->delete($relationType, $this->getUser());

      $this->addFlash('success', $trans->trans('relation-type.removed', ['%item%' => $relationType->getName()]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    return $this->render('relation_type/remove.html.twig', [
      'relationType'     => $relationType,
      'conceptRelations' => $conceptRelationRepository->getByRelationType($relationType),
      'form'             => $form->createView(),
    ]);
  }

  private function getHandler(): RelationTypeHandler
  {
    // Double validation is not needed as we rely on the form validation
    return new RelationTypeHandler($this->em, null, $this->reviewService);
  }
}
