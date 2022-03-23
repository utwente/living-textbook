<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\ExternalResource;
use App\Entity\PendingChange;
use App\Form\ExternalResource\EditExternalResourceType;
use App\Form\Type\RemoveType;
use App\Repository\ExternalResourceRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ExternalResourceController.
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/externalresource", requirements={"_studyArea"="\d+"})
 */
class ExternalResourceController extends AbstractController
{
  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_externalresource_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function add(
      Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $externalResource = (new ExternalResource())->setStudyArea($studyArea);
    $snapshot         = $reviewService->getSnapshot($externalResource);

    $form = $this->createForm(EditExternalResourceType::class, $externalResource, ['studyArea' => $studyArea]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $externalResource, PendingChange::CHANGE_TYPE_ADD, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('external-resource.saved', ['%item%' => $externalResource->getTitle()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_externalresource_list');
    }

    return [
        'externalResource' => $externalResource,
        'form'             => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{externalResource}", requirements={"externalResource"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_externalresource_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, ExternalResource $externalResource,
      ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($externalResource->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be edited
    if (!$reviewService->canObjectBeEdited($studyArea, $externalResource)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
          '%item%' => $trans->trans('external-resource._name'),
      ]));

      return $this->redirectToRoute('app_externalresource_list');
    }

    // Create snapshot
    $snapshot = $reviewService->getSnapshot($externalResource);

    // Create form and handle request
    $form = $this->createForm(EditExternalResourceType::class, $externalResource, [
        'studyArea'           => $studyArea,
        'pending_change_info' => $reviewService->getPendingChangeObjectInformation($studyArea, $externalResource),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $externalResource, PendingChange::CHANGE_TYPE_EDIT, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('external-resource.updated', ['%item%' => $externalResource->getTitle()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_externalresource_list');
    }

    return [
        'externalResource' => $externalResource,
        'form'             => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return array
   */
  public function list(RequestStudyArea $requestStudyArea, ExternalResourceRepository $repo)
  {
    return [
        'studyArea'         => $requestStudyArea->getStudyArea(),
        'externalResources' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{externalResource}", requirements={"externalResource"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_externalresource_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, ExternalResource $externalResource,
      ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($externalResource->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be deleted
    if (!$reviewService->canObjectBeRemoved($studyArea, $externalResource)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
          '%item%' => $trans->trans('external-resource._name'),
      ]));

      return $this->redirectToRoute('app_externalresource_list');
    }

    $form = $this->createForm(RemoveType::class, null, [
        'cancel_route' => 'app_externalresource_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $externalResource, PendingChange::CHANGE_TYPE_REMOVE);

      $this->addFlash('success', $trans->trans('external-resource.removed', ['%item%' => $externalResource->getTitle()]));

      return $this->redirectToRoute('app_externalresource_list');
    }

    return [
        'externalResource' => $externalResource,
        'form'             => $form->createView(),
    ];
  }
}
