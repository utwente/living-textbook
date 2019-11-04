<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\PendingChange;
use App\Entity\RelationType;
use App\Form\RelationType\EditRelationTypeType;
use App\Form\Type\RemoveType;
use App\Repository\ConceptRelationRepository;
use App\Repository\RelationTypeRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class RelationTypeController
 *
 * @Route("/{_studyArea}/relationtype", requirements={"_studyArea"="\d+"})
 */
class RelationTypeController extends AbstractController
{
  /**
   * @Route("/add")
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_relationtype_list", subject="requestStudyArea")
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

    // Create new
    $relationType = (new RelationType())->setStudyArea($studyArea);
    $snapshot     = $reviewService->getSnapshot($relationType);

    $form = $this->createForm(EditRelationTypeType::class, $relationType);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $relationType, PendingChange::CHANGE_TYPE_ADD, $snapshot);

      $this->addFlash('success', $trans->trans('relation-type.saved', ['%item%' => $relationType->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_relationtype_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{relationType}", requirements={"relationType"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_relationtype_list", subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param RelationType        $relationType
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return Response|array
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, RelationType $relationType, ReviewService $reviewService,
      TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($relationType->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Check if not removed
    if ($relationType->getDeletedAt() !== NULL) {
      throw $this->createNotFoundException();
    }

    $snapshot = $reviewService->getSnapshot($relationType);

    // Create form and handle request
    $form = $this->createForm(EditRelationTypeType::class, $relationType);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Save the data
      $reviewService->storeChange($studyArea, $relationType, PendingChange::CHANGE_TYPE_EDIT, $snapshot);

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
   * @param RequestStudyArea       $requestStudyArea
   * @param RelationTypeRepository $repo
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
   *
   * @param Request                   $request
   * @param RequestStudyArea          $requestStudyArea
   * @param RelationType              $relationType
   * @param ConceptRelationRepository $conceptRelationRepository
   * @param ReviewService             $reviewService
   * @param TranslatorInterface       $trans
   *
   * @return array|RedirectResponse
   * @throws \Exception
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, RelationType $relationType,
      ConceptRelationRepository $conceptRelationRepository, ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($relationType->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Check if not already deleted
    if ($relationType->getDeletedAt() !== NULL) {
      $this->addFlash('warning', $trans->trans('relation-type.removed-already', ['%item%' => $relationType->getName()]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_relationtype_list',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      // This must be registered as remove change, but it must be handled differently when actually removed
      $reviewService->storeChange($studyArea, $relationType, PendingChange::CHANGE_TYPE_REMOVE, NULL,
          function () use (&$relationType) {
            // Remove the relation type by setting the deletedAt/By manually
            $relationType->setDeletedAt(new DateTime());
            $relationType->setDeletedBy($this->getUser() instanceof UserInterface ? $this->getUser()->getUsername() : 'anon.');
          });

      $this->addFlash('success', $trans->trans('relation-type.removed', ['%item%' => $relationType->getName()]));

      return $this->redirectToRoute('app_relationtype_list');
    }

    return [
        'relationType'     => $relationType,
        'conceptRelations' => $conceptRelationRepository->getByRelationType($relationType),
        'form'             => $form->createView(),
    ];
  }
}
