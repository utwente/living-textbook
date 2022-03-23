<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\Abbreviation;
use App\Entity\PendingChange;
use App\Form\Abbreviation\EditAbbreviationType;
use App\Form\Type\RemoveType;
use App\Repository\AbbreviationRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class AbbreviationController.
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/abbreviation", requirements={"_studyArea"="\d+"})
 */
class AbbreviationController extends AbstractController
{
  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_abbreviation_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function add(
      Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $abbreviation = (new Abbreviation())->setStudyArea($studyArea);
    $snapshot     = $reviewService->getSnapshot($abbreviation);

    $form = $this->createForm(EditAbbreviationType::class, $abbreviation, ['studyArea' => $studyArea]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $abbreviation, PendingChange::CHANGE_TYPE_ADD, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('abbreviation.saved', ['%item%' => $abbreviation->getAbbreviation()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_abbreviation_list');
    }

    return [
        'abbreviation' => $abbreviation,
        'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/data", options={"expose"="true"})
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return JsonResponse
   */
  public function data(Request $request, RequestStudyArea $requestStudyArea, AbbreviationRepository $abbreviationRepo, SerializerInterface $serializer)
  {
    // Retrieve the abbreviations
    $ids           = $request->query->get('ids');
    $ids           = array_filter($ids, function ($id) {
      return is_numeric($id);
    });
    $abbreviations = $abbreviationRepo->findBy(['id' => $ids, 'studyArea' => $requestStudyArea->getStudyArea()]);

    $json = $serializer->serialize($abbreviations, 'json');

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /**
   * @Route("/edit/{abbreviation}", requirements={"abbreviation"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_abbreviation_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, Abbreviation $abbreviation, ReviewService $reviewService,
      TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($abbreviation->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be edited
    if (!$reviewService->canObjectBeEdited($studyArea, $abbreviation)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
          '%item%' => $trans->trans('abbreviation._name'),
      ]));

      return $this->redirectToRoute('app_abbreviation_list');
    }

    // Create snapshot
    $snapshot = $reviewService->getSnapshot($abbreviation);

    // Create form and handle request
    $form = $this->createForm(EditAbbreviationType::class, $abbreviation, [
        'studyArea'           => $studyArea,
        'pending_change_info' => $reviewService->getPendingChangeObjectInformation($studyArea, $abbreviation),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $abbreviation, PendingChange::CHANGE_TYPE_EDIT, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('abbreviation.updated', ['%item%' => $abbreviation->getAbbreviation()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_abbreviation_list');
    }

    return [
        'abbreviation' => $abbreviation,
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
  public function list(RequestStudyArea $requestStudyArea, AbbreviationRepository $repo)
  {
    return [
        'studyArea'     => $requestStudyArea->getStudyArea(),
        'abbreviations' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{abbreviation}", requirements={"abbreviation"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_abbreviation_list", subject="requestStudyArea")
   *
   * @return array|Response
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, Abbreviation $abbreviation, ReviewService $reviewService,
      TranslatorInterface $trans)
  {
    $studyArea = $abbreviation->getStudyArea();

    // Check if correct study area
    if ($studyArea->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Verify it can be deleted
    if (!$reviewService->canObjectBeRemoved($studyArea, $abbreviation)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
          '%item%' => $trans->trans('abbreviation._name'),
      ]));

      return $this->redirectToRoute('app_abbreviation_list');
    }

    $form = $this->createForm(RemoveType::class, null, [
        'cancel_route' => 'app_abbreviation_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $reviewService->storeChange($studyArea, $abbreviation, PendingChange::CHANGE_TYPE_REMOVE);

      $this->addFlash('success', $trans->trans('abbreviation.removed', ['%item%' => $abbreviation->getAbbreviation()]));

      return $this->redirectToRoute('app_abbreviation_list');
    }

    return [
        'abbreviation' => $abbreviation,
        'form'         => $form->createView(),
    ];
  }
}
