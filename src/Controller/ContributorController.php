<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\Contributor;
use App\Form\Contributor\EditContributorType;
use App\Form\Type\RemoveType;
use App\Repository\ContributorRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ContributorController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/contributor", requirements={"_studyArea"="\d+"})
 */
class ContributorController extends AbstractController
{

  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_contributor_list", subject="requestStudyArea")
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
    $contributor = (new Contributor())->setStudyArea($requestStudyArea->getStudyArea());

    $form = $this->createForm(EditContributorType::class, $contributor, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($contributor);
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('contributor.saved', ['%item%' => $contributor->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_contributor_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{contributor}", requirements={"contributor"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_contributor_list", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Contributor            $contributor
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function edit(Request $request, RequestStudyArea $requestStudyArea, Contributor $contributor, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if correct study area
    if ($contributor->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Create form and handle request
    $form = $this->createForm(EditContributorType::class, $contributor, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('contributor.updated', ['%item%' => $contributor->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_contributor_list');
    }

    return [
        'contributor' => $contributor,
        'form'        => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea      $requestStudyArea
   * @param ContributorRepository $repo
   *
   * @return array
   */
  public function list(RequestStudyArea $requestStudyArea, ContributorRepository $repo)
  {
    return [
        'studyArea'    => $requestStudyArea->getStudyArea(),
        'contributors' => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{contributor}", requirements={"contributor"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_contributor_list", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Contributor            $contributor
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function remove(Request $request, RequestStudyArea $requestStudyArea, Contributor $contributor, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if correct study area
    if ($contributor->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_contributor_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->remove($contributor);
      $em->flush();

      $this->addFlash('success', $trans->trans('contributor.removed', ['%item%' => $contributor->getName()]));

      return $this->redirectToRoute('app_contributor_list');
    }

    return [
        'contributor' => $contributor,
        'form'        => $form->createView(),
    ];
  }

}
