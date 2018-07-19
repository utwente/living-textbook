<?php

namespace App\Controller;

use App\Entity\Abbreviation;
use App\Form\Abbreviation\EditAbbreviationType;
use App\Form\Type\RemoveType;
use App\Repository\AbbreviationRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AbbreviationController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/abbreviation", requirements={"_studyArea"="\d+"})
 */
class AbbreviationController extends Controller
{

  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
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
    $abbreviation = (new Abbreviation())->setStudyArea($requestStudyArea->getStudyArea());

    $form = $this->createForm(EditAbbreviationType::class, $abbreviation, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($abbreviation);
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('abbreviation.saved', ['%item%' => $abbreviation->getAbbreviation()]));

      return $this->redirectToRoute('app_abbreviation_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{abbreviation}", requirements={"abbreviation"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Abbreviation           $abbreviation
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function edit(Request $request, RequestStudyArea $requestStudyArea, Abbreviation $abbreviation, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if correct study area
    if ($abbreviation->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Create form and handle request
    $form = $this->createForm(EditAbbreviationType::class, $abbreviation, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('abbreviation.updated', ['%item%' => $abbreviation->getAbbreviation()]));

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
   * @param RequestStudyArea       $requestStudyArea
   * @param AbbreviationRepository $repo
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
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Abbreviation           $abbreviation
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function remove(Request $request, RequestStudyArea $requestStudyArea, Abbreviation $abbreviation, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if correct study area
    if ($abbreviation->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_abbreviation_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->remove($abbreviation);
      $em->flush();

      $this->addFlash('success', $trans->trans('abbreviation.removed', ['%item%' => $abbreviation->getAbbreviation()]));

      return $this->redirectToRoute('app_abbreviation_list');
    }

    return [
        'abbreviation' => $abbreviation,
        'form'         => $form->createView(),
    ];
  }

}
