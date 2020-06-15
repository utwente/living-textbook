<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\Tag;
use App\Form\Tag\EditTagType;
use App\Form\Type\RemoveType;
use App\Repository\TagRepository;
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
 * Class TagController
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/tag", requirements={"_studyArea"="\d+"})
 */
class TagController extends AbstractController
{

  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_tag_list", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function add(
      Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $tag = (new Tag())->setStudyArea($studyArea);

    $form = $this->createForm(EditTagType::class, $tag, ['studyArea' => $studyArea]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->persist($tag);
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('tag.saved', ['%item%' => $tag->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_tag_list');
    }

    return [
        'tag'  => $tag,
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/edit/{tag}", requirements={"tag"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_tag_list", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Tag                    $tag
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function edit(
      Request $request, RequestStudyArea $requestStudyArea, Tag $tag,
      EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($tag->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    // Create form and handle request
    $form = $this->createForm(EditTagType::class, $tag, [
        'studyArea' => $studyArea,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $em->flush();

      // Return to list
      $this->addFlash('success', $trans->trans('tag.updated', ['%item%' => $tag->getName()]));

      // Always return to list as there is no show
      return $this->redirectToRoute('app_tag_list');
    }

    return [
        'tag'  => $tag,
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/list")
   * @Template()
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param RequestStudyArea $requestStudyArea
   * @param TagRepository    $repo
   *
   * @return array
   */
  public function list(RequestStudyArea $requestStudyArea, TagRepository $repo)
  {
    return [
        'studyArea' => $requestStudyArea->getStudyArea(),
        'tags'      => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ];
  }

  /**
   * @Route("/remove/{tag}", requirements={"tag"="\d+"})
   * @Template()
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_tag_list", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param Tag                    $tag
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function remove(
      Request $request, RequestStudyArea $requestStudyArea, Tag $tag,
      EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($tag->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_tag_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Remove tag from default if set
      if ($studyArea->getDefaultTagFilter()->getId() === $tag->getId()) {
        $studyArea->setDefaultTagFilter(NULL);
      }

      // Save the data
      $em->remove($tag);
      $em->flush();

      $this->addFlash('success', $trans->trans('tag.removed', ['%item%' => $tag->getName()]));

      return $this->redirectToRoute('app_tag_list');
    }

    return [
        'tag'  => $tag,
        'form' => $form->createView(),
    ];
  }

}
