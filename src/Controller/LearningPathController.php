<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\LearningPath;
use App\Form\LearningPath\EditLearningPathType;
use App\Form\Type\RemoveType;
use App\Form\Type\SaveType;
use App\Repository\LearningPathRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function add(Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Create new object
    $learningPath = (new LearningPath())->setStudyArea($requestStudyArea->getStudyArea());

    $form = $this->createForm(EditLearningPathType::class, $learningPath, [
        'studyArea'    => $requestStudyArea->getStudyArea(),
        'learningPath' => $learningPath,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($learningPath);
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('learning-path.saved', ['%item%' => $learningPath->getName()]));

      return $this->redirectToRoute('app_learningpath_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{learningPath}", requirements={"learningPath"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_learningpath_show", routeParams={"learningPath"="{learningPath}"},
   *                                                       subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param LearningPath           $learningPath
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function edit(Request $request, RequestStudyArea $requestStudyArea, LearningPath $learningPath,
                       EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $this->verifyCorrectStudyArea($requestStudyArea, $learningPath);

    $originalElements = new ArrayCollection();
    foreach ($learningPath->getElements() as $element) {
      $originalElements->add($element);
    }

    // Create form and handle request
    $form = $this->createForm(EditLearningPathType::class, $learningPath, [
        'studyArea'     => $requestStudyArea->getStudyArea(),
        'learningPath'  => $learningPath,
        'save-and-list' => true,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Remove elements no longer used
      foreach ($originalElements as $element) {
        if (false === $learningPath->getElements()->contains($element)) {
          $em->remove($element);
        }
      }

      // Save the data
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('learning-path.updated', ['%item%' => $learningPath->getName()]));

      if (SaveType::isListClicked($form)) {
        return $this->redirectToRoute('app_learningpath_list');
      } else {
        return $this->redirectToRoute('app_learningpath_edit', ['learningPath' => $learningPath->getId()]);
      }
    }

    return [
        'learningPath' => $learningPath,
        'form'         => $form->createView(),
    ];
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
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param LearningPath           $learningPath
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function remove(Request $request, RequestStudyArea $requestStudyArea, LearningPath $learningPath,
                         EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $this->verifyCorrectStudyArea($requestStudyArea, $learningPath);

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_learningpath_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid() && RemoveType::isRemoveClicked($form)) {
      $em->remove($learningPath);
      $em->flush();

      $this->addFlash('success', $trans->trans('learning-path.removed', ['%item%' => $learningPath->getName()]));

      return $this->redirectToRoute('app_learningpath_list');
    }

    return [
        'learningPath' => $learningPath,
        'form'         => $form->createView(),
    ];
  }

  /**
   * @Route("/show/{learningPath}", requirements={"learningPath"="\d+"})
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
