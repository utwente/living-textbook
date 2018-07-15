<?php

namespace App\Controller;

use App\Entity\ExternalResource;
use App\Form\ExternalResource\EditExternalResourceType;
use App\Form\Type\RemoveType;
use App\Repository\ExternalResourceRepository;
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
 * Class ExternalResourceController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/externalresource", requirements={"_studyArea"="\d+"})
 */
class ExternalResourceController extends Controller
{

  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
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
    $externalResource = (new ExternalResource())->setStudyArea($requestStudyArea->getStudyArea());

    $form = $this->createForm(EditExternalResourceType::class, $externalResource, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($externalResource);
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('external-resource.saved', ['%item%' => $externalResource->getTitle()]));

      return $this->redirectToRoute('app_externalresource_list');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{externalResource}", requirements={"externalResource"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param ExternalResource       $externalResource
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function edit(Request $request, RequestStudyArea $requestStudyArea, ExternalResource $externalResource, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if correct study area
    if ($externalResource->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    // Create form and handle request
    $form = $this->createForm(EditExternalResourceType::class, $externalResource, ['studyArea' => $requestStudyArea->getStudyArea()]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('external-resource.updated', ['%item%' => $externalResource->getTitle()]));

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
   * @param RequestStudyArea           $requestStudyArea
   * @param ExternalResourceRepository $repo
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
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param ExternalResource       $externalResource
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function remove(Request $request, RequestStudyArea $requestStudyArea, ExternalResource $externalResource, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if correct study area
    if ($externalResource->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_externalresource_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->remove($externalResource);
      $em->flush();

      $this->addFlash('success', $trans->trans('external-resource.removed', ['%item%' => $externalResource->getTitle()]));

      return $this->redirectToRoute('app_externalresource_list');
    }

    return [
        'externalResource' => $externalResource,
        'form'             => $form->createView(),
    ];
  }

}
