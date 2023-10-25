<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\LearningPath;
use App\Entity\PendingChange;
use App\Form\LearningPath\EditLearningPathType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\LearningPathRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Review\ReviewService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class LearningPathController.
 *
 * @Route("/{_studyArea}/learningpath", requirements={"_studyArea"="\d+"})
 */
class LearningPathController extends AbstractController
{
  /**
   * @Route("/add")
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @DenyOnFrozenStudyArea(route="app_learningpath_list", subject="requestStudyArea")
   */
  public function add(
    Request $request, RequestStudyArea $requestStudyArea, ReviewService $reviewService, TranslatorInterface $trans): array|Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $learningPath = (new LearningPath())->setStudyArea($studyArea);
    $snapshot     = $reviewService->getSnapshot($learningPath);

    $form = $this->createForm(EditLearningPathType::class, $learningPath, [
      'studyArea'    => $studyArea,
      'learningPath' => $learningPath,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $learningPath, PendingChange::CHANGE_TYPE_ADD, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('learning-path.saved', ['%item%' => $learningPath->getName()]));

      if (!$learningPath->getId() || SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_learningpath_list');
      }

      return $this->redirectToRoute('app_learningpath_show', ['learningPath' => $learningPath->getId()]);
    }

    return [
      'learningPath' => $learningPath,
      'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{learningPath}", requirements={"learningPath"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @DenyOnFrozenStudyArea(route="app_learningpath_show",
   *   routeParams={"learningPath"="{learningPath}"}, subject="requestStudyArea")
   */
  public function edit(
    Request $request, RequestStudyArea $requestStudyArea, LearningPath $learningPath, ReviewService $reviewService,
    EntityManagerInterface $em, TranslatorInterface $trans): array|Response
  {
    $this->verifyCorrectStudyArea($requestStudyArea, $learningPath);
    $studyArea = $requestStudyArea->getStudyArea();

    $originalElements = new ArrayCollection();
    foreach ($learningPath->getElements() as $element) {
      $originalElements->add($element);
    }

    // Verify it can be edited
    if (!$reviewService->canObjectBeEdited($studyArea, $learningPath)) {
      $this->addFlash('error', $trans->trans('review.edit-not-possible', [
        '%item%' => $trans->trans('learning-path._name'),
      ]));

      return $this->redirectToRoute('app_learningpath_list');
    }

    // Create snapshot
    $snapshot = $reviewService->getSnapshot($learningPath);

    // Create form and handle request
    $form = $this->createForm(EditLearningPathType::class, $learningPath, [
      'studyArea'           => $studyArea,
      'learningPath'        => $learningPath,
      'pending_change_info' => $reviewService->getPendingChangeObjectInformation($studyArea, $learningPath),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($studyArea, $learningPath, PendingChange::CHANGE_TYPE_EDIT, $snapshot,
        function (LearningPath $learningPath) use (&$originalElements, &$em) {
          // Remove elements no longer used
          foreach ($originalElements as $element) {
            if (false === $learningPath->getElements()->contains($element)) {
              $em->remove($element);
            }
          }
        });

      // Return to list
      $this->addFlash('success', $trans->trans('learning-path.updated', ['%item%' => $learningPath->getName()]));

      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_learningpath_list');
      }

      return $this->redirectToRoute('app_learningpath_show', ['learningPath' => $learningPath->getId()]);
    }

    return [
      'learningPath' => $learningPath,
      'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/data/{learningPath}", options={"expose"=true}, requirements={"learningPath"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return JsonResponse
   */
  public function data(
    /* @noinspection PhpUnusedParameterInspection Used for auth */
    RequestStudyArea $requestStudyArea,
    LearningPath $learningPath, SerializerInterface $serializer)
  {
    /** @phan-suppress-next-line PhanTypeMismatchArgument */
    $json = $serializer->serialize($learningPath, 'json', SerializationContext::create()->setGroups(['Default']));

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /**
   * @Route("/list")
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return array
   */
  public function list(RequestStudyArea $requestStudyArea, LearningPathRepository $repository)
  {
    return [
      'studyArea'     => $requestStudyArea->getStudyArea(),
      'learningPaths' => $repository->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{learningPath}", requirements={"learningPath"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @DenyOnFrozenStudyArea(route="app_learningpath_show", routeParams={"learningPath"="{learningPath}"},
   *                                                       subject="requestStudyArea")
   */
  public function remove(
    Request $request, RequestStudyArea $requestStudyArea, LearningPath $learningPath, ReviewService $reviewService,
    TranslatorInterface $trans): array|RedirectResponse
  {
    $this->verifyCorrectStudyArea($requestStudyArea, $learningPath);
    $studyArea = $requestStudyArea->getStudyArea();

    // Verify it can be deleted
    if (!$reviewService->canObjectBeRemoved($studyArea, $learningPath)) {
      $this->addFlash('error', $trans->trans('review.remove-not-possible', [
        '%item%' => $trans->trans('learning-path._name'),
      ]));

      return $this->redirectToRoute('app_learningpath_list');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route'        => 'app_learningpath_show',
      'cancel_route_params' => ['learningPath' => $learningPath->getId()],
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && RemoveType::isRemoveClicked($form)) {
      $reviewService->storeChange($studyArea, $learningPath, PendingChange::CHANGE_TYPE_REMOVE);

      $this->addFlash('success', $trans->trans('learning-path.removed', ['%item%' => $learningPath->getName()]));

      return $this->redirectToRoute('app_learningpath_list');
    }

    return [
      'learningPath' => $learningPath,
      'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/show/{learningPath}", options={"expose"=true}, requirements={"learningPath"="\d+"})
   *
   * @Template()
   *
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @return array
   */
  public function show(RequestStudyArea $requestStudyArea, LearningPath $learningPath)
  {
    $this->verifyCorrectStudyArea($requestStudyArea, $learningPath);

    return [
      'learningPath' => $learningPath,
    ];
  }

  private function verifyCorrectStudyArea(RequestStudyArea $requestStudyArea, LearningPath $learningPath)
  {
    // Check if correct study area
    if ($learningPath->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }
  }
}
