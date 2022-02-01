<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\Tag;
use App\EntityHandler\TagHandler;
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
 * @Route("/{_studyArea}/tag", requirements={"_studyArea"="\d+"})
 */
class TagController extends AbstractController
{
  public function __construct(
      private readonly EntityManagerInterface $em
  )
  {
  }

  /**
   * @Route("/add")
   * @Template
   * @IsGranted("STUDYAREA_EDIT", subject="requestStudyArea")
   * @DenyOnFrozenStudyArea(route="app_tag_list", subject="requestStudyArea")
   */
  public function add(
      Request             $request,
      RequestStudyArea    $requestStudyArea,
      TranslatorInterface $trans): Response|array
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Create new object
    $tag = (new Tag())->setStudyArea($studyArea);

    $form = $this->createForm(EditTagType::class, $tag, ['studyArea' => $studyArea]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $this->getHandler()->add($tag);

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
   */
  public function edit(
      Request             $request,
      RequestStudyArea    $requestStudyArea,
      Tag                 $tag,
      TranslatorInterface $trans): Response|array
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
      $this->getHandler()->update($tag);

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
   */
  public function list(RequestStudyArea $requestStudyArea, TagRepository $repo): array
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
   */
  public function remove(
      Request             $request,
      RequestStudyArea    $requestStudyArea,
      Tag                 $tag,
      TranslatorInterface $trans): Response|array
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
      // Save the data
      $this->getHandler()->delete($tag);

      $this->addFlash('success', $trans->trans('tag.removed', ['%item%' => $tag->getName()]));

      return $this->redirectToRoute('app_tag_list');
    }

    return [
        'tag'  => $tag,
        'form' => $form->createView(),
    ];
  }

  private function getHandler(): TagHandler
  {
    // Double validation is not needed as we rely on the form validation, review service does not hold for tags
    return new TagHandler($this->em, NULL, NULL);
  }
}
