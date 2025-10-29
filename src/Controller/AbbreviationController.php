<?php

namespace App\Controller;

use App\Attribute\DenyOnFrozenStudyArea;
use App\Entity\Abbreviation;
use App\Entity\PendingChange;
use App\Form\Abbreviation\EditAbbreviationType;
use App\Form\Type\RemoveType;
use App\Repository\AbbreviationRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use App\Security\Voters\StudyAreaVoter;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_filter;
use function is_numeric;

#[Route('/{_studyArea<\d+>}/abbreviation')]
class AbbreviationController extends AbstractController
{
  #[Route('/add')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_abbreviation_list', subject: 'requestStudyArea')]
  public function add(
    Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $abbreviation = new Abbreviation()->setStudyArea($studyArea);
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

    return $this->render('abbreviation/add.html.twig', [
      'abbreviation' => $abbreviation,
      'form'         => $form,
    ]);
  }

  #[Route('/data', options: ['expose' => 'true'])]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function data(Request $request, RequestStudyArea $requestStudyArea, AbbreviationRepository $abbreviationRepo, SerializerInterface $serializer): Response
  {
    // Retrieve the abbreviations
    $ids           = $request->query->all('ids');
    $ids           = array_filter($ids, is_numeric(...));
    $abbreviations = $abbreviationRepo->findBy(['id' => $ids, 'studyArea' => $requestStudyArea->getStudyArea()]);

    $json = $serializer->serialize($abbreviations, 'json');

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  #[Route('/edit/{abbreviation<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_abbreviation_list', subject: 'requestStudyArea')]
  public function edit(
    Request $request, RequestStudyArea $requestStudyArea, Abbreviation $abbreviation, ReviewService $reviewService,
    TranslatorInterface $trans): Response
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

    return $this->render('abbreviation/edit.html.twig', [
      'abbreviation' => $abbreviation,
      'form'         => $form,
    ]);
  }

  #[Route('/list')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(RequestStudyArea $requestStudyArea, AbbreviationRepository $repo): Response
  {
    return $this->render('abbreviation/list.html.twig', [
      'studyArea'     => $requestStudyArea->getStudyArea(),
      'abbreviations' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ]);
  }

  #[Route('/remove/{abbreviation<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  #[DenyOnFrozenStudyArea(route: 'app_abbreviation_list', subject: 'requestStudyArea')]
  public function remove(
    Request $request, RequestStudyArea $requestStudyArea, Abbreviation $abbreviation, ReviewService $reviewService,
    TranslatorInterface $trans): Response
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

    return $this->render('abbreviation/remove.html.twig', [
      'abbreviation' => $abbreviation,
      'form'         => $form,
    ]);
  }
}
