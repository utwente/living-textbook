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
 * Class LearningPathController
 *
 * @Route("/{_studyArea}/learningpath", requirements={"_studyArea"="\d+"})
 */
class LearningPathController extends AbstractController
{

  /**
   * @Route("/add")
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningpath_list", subject="requestStudyArea")
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
      $reviewService->storeChange($studyArea, $learningPath, PendingChange::CHANGE_TYPE_ADD, NULL, $snapshot);

      // Return to list
      $this->addFlash('success', $trans->trans('learning-path.saved', ['%item%' => $learningPath->getName()]));

      if (!$learningPath->getId() || SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_learningpath_list');
      }

      return $this->redirectToRoute('app_learningpath_show', ['learningPath' => $learningPath->getId()]);
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{learningPath}", requirements={"learningPath"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningpath_show",
   *   routeParams={"learningPath"="{learningPath}"}, subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param LearningPath           $learningPath
   * @param ReviewService          $reviewService
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, LearningPath $learningPath, ReviewService $reviewService,
      EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $this->verifyCorrectStudyArea($requestStudyArea, $learningPath);

    $originalElements = new ArrayCollection();
    foreach ($learningPath->getElements() as $element) {
      $originalElements->add($element);
    }
    $snapshot = $reviewService->getSnapshot($learningPath);

    // Create form and handle request
    $form = $this->createForm(EditLearningPathType::class, $learningPath, [
        'studyArea'    => $requestStudyArea->getStudyArea(),
        'learningPath' => $learningPath,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $reviewService->storeChange($requestStudyArea->getStudyArea(), $learningPath, PendingChange::CHANGE_TYPE_EDIT,
          function (LearningPath $learningPath) use (&$originalElements, &$em) {
            // Remove elements no longer used
            foreach ($originalElements as $element) {
              if (false === $learningPath->getElements()->contains($element)) {
                $em->remove($element);
              }
            }
          }, $snapshot);

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
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea    $requestStudyArea
   * @param LearningPath        $learningPath
   * @param SerializerInterface $serializer
   *
   * @return JsonResponse
   */
  public function data(
    /** @noinspection PhpUnusedParameterInspection Used for auth */
      RequestStudyArea $requestStudyArea,
      LearningPath $learningPath, SerializerInterface $serializer)
  {
    /** @phan-suppress-next-line PhanTypeMismatchArgument */
    $json = $serializer->serialize($learningPath, 'json', SerializationContext::create()->setGroups(['Default']));

    return new JsonResponse($json, Response::HTTP_OK, [], true);
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea       $requestStudyArea
   * @param LearningPathRepository $repository
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
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningpath_show", routeParams={"learningPath"="{learningPath}"},
   *                                                       subject="requestStudyArea")
   *
   * @param Request             $request
   * @param RequestStudyArea    $requestStudyArea
   * @param LearningPath        $learningPath
   * @param ReviewService       $reviewService
   * @param TranslatorInterface $trans
   *
   * @return array|RedirectResponse
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, LearningPath $learningPath, ReviewService $reviewService,
      TranslatorInterface $trans)
  {
    $this->verifyCorrectStudyArea($requestStudyArea, $learningPath);

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route'        => 'app_learningpath_show',
        'cancel_route_params' => ['learningPath' => $learningPath->getId()],
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && RemoveType::isRemoveClicked($form)) {
      $reviewService->storeChange($requestStudyArea->getStudyArea(), $learningPath, PendingChange::CHANGE_TYPE_REMOVE);

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
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea $requestStudyArea
   * @param LearningPath     $learningPath
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
