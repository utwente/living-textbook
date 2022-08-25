<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\RelationType;
use App\EntityHandler\RelationTypeHandler;
use App\Form\RelationType\EditRelationTypeType;
use App\Form\Type\RemoveType;
use App\Repository\ConceptRelationRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RelationTypeController.
 *
 * @Route("/{_studyArea}/relationtype", requirements={"_studyArea"="\d+"})
 */
class RelationTypeController extends AbstractController
{
  public function __construct(
      private readonly EntityManagerInterface $em,
      private readonly ReviewService $reviewService,
  ) {
  }

  /**
   * @Route("/add")
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_relationtype_list", subject="requestStudyArea")
   */
  public function add(
      Request $request,
      RequestStudyArea $requestStudyArea,
      TranslatorInterface $trans): Response|array
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

    return [
        'relationType' => $relationType,
        'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{relationType}", requirements={"relationType"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_relationtype_list", subject="requestStudyArea")
   * @noRector Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector
   */
  public function edit(
      Request $request,
      RequestStudyArea $requestStudyArea,
      RelationType $relationType,
      TranslatorInterface $trans): Response|array
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

    return [
        'relationType' => $relationType,
        'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return array
   */
  public function list(RequestStudyArea $requestStudyArea, RelationTypeRepository $repo)
  {
    return [
        'studyArea'     => $requestStudyArea->getStudyArea(),
        'relationTypes' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{relationType}", requirements={"relationType"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_relationtype_list", subject="requestStudyArea")
   * @noRector Rector\Php56\Rector\FunctionLike\AddDefaultValueForUndefinedVariableRector
   */
  public function remove(
      Request $request,
      RequestStudyArea $requestStudyArea,
      RelationType $relationType,
      ConceptRelationRepository $conceptRelationRepository,
      TranslatorInterface $trans)
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

    return [
        'relationType'     => $relationType,
        'conceptRelations' => $conceptRelationRepository->getByRelationType($relationType),
        'form'             => $form->createView(),
    ];
  }

  private function getHandler(): RelationTypeHandler
  {
    // Double validation is not needed as we rely on the form validation
    return new RelationTypeHandler($this->em, null, $this->reviewService);
  }
}
