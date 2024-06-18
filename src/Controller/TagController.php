<?php

namespace App\Controller;

use App\Annotation\DenyOnFrozenStudyArea;
use App\Entity\Tag;
use App\EntityHandler\TagHandler;
use App\Form\Tag\EditTagType;
use App\Form\Type\RemoveType;
use App\Repository\TagRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\Voters\StudyAreaVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/{_studyArea<\d+>}/tag')]
class TagController extends AbstractController
{
  public function __construct(
    private readonly EntityManagerInterface $em
  ) {
  }

  /** @DenyOnFrozenStudyArea(route="app_tag_list", subject="requestStudyArea") */
  #[Route('/add')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function add(
    Request $request,
    RequestStudyArea $requestStudyArea,
    TranslatorInterface $trans): Response
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

    return $this->render('tag/add.html.twig', [
      'tag'  => $tag,
      'form' => $form,
    ]);
  }

  /** @DenyOnFrozenStudyArea(route="app_tag_list", subject="requestStudyArea") */
  #[Route('/edit/{tag<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function edit(
    Request $request,
    RequestStudyArea $requestStudyArea,
    Tag $tag,
    TranslatorInterface $trans): Response
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

    return $this->render('tag/edit.html.twig', [
      'tag'  => $tag,
      'form' => $form,
    ]);
  }

  #[Route('/show/{tag<\d+>}')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function show(RequestStudyArea $requestStudyArea, Tag $tag): Response
  {
    // Check if correct study area
    if ($tag->getStudyArea()->getId() != $requestStudyArea->getStudyArea()->getId()) {
      throw $this->createNotFoundException();
    }

    return $this->render('tag/show.html.twig', [
      'tag' => $tag,
    ]);
  }

  #[Route('/list')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function list(RequestStudyArea $requestStudyArea, TagRepository $repo): Response
  {
    return $this->render('tag/list.html.twig', [
      'studyArea' => $requestStudyArea->getStudyArea(),
      'tags'      => $repo->findForStudyArea($requestStudyArea->getStudyArea()),
    ]);
  }

  /** @DenyOnFrozenStudyArea(route="app_tag_list", subject="requestStudyArea") */
  #[Route('/remove/{tag<\d+>}')]
  #[IsGranted(StudyAreaVoter::EDIT, subject: 'requestStudyArea')]
  public function remove(
    Request $request,
    RequestStudyArea $requestStudyArea,
    Tag $tag,
    TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Check if correct study area
    if ($tag->getStudyArea()->getId() != $studyArea->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_tag_list',
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Save the data
      $this->getHandler()->delete($tag);

      $this->addFlash('success', $trans->trans('tag.removed', ['%item%' => $tag->getName()]));

      return $this->redirectToRoute('app_tag_list');
    }

    return $this->render('tag/remove.html.twig', [
      'tag'  => $tag,
      'form' => $form,
    ]);
  }

  private function getHandler(): TagHandler
  {
    // Double validation is not needed as we rely on the form validation, review service does not hold for tags
    return new TagHandler($this->em, null, null);
  }
}
