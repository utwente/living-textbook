<?php

namespace App\Controller;

use App\Entity\Help;
use App\Entity\User;
use App\Form\Help\EditHelpType;
use App\Repository\HelpRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Scoped per study area to prevent reload issues.
 */
#[Route('/{_studyArea<\d+>}/help')]
class HelpController extends AbstractController
{
  /**
   * Displays the available help documents.
   *
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  #[Route]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function index(HelpRepository $helpRepository): Response
  {
    return $this->render('help/index.html.twig', [
      'help' => $helpRepository->getCurrent(),
    ]);
  }

  /**
   * Edit the help page.
   *
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  #[Route('/edit')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function edit(
    Request $request, HelpRepository $helpRepository, EntityManagerInterface $em, TranslatorInterface $translator): Response
  {
    $help = $helpRepository->getCurrent();
    $form = $this->createForm(EditHelpType::class, $help);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Create a new help version
      $newHelp = (new Help())
        ->setContent($help->getContent());
      $em->persist($newHelp);

      // Save the data
      $em->flush();
      $this->addFlash('success', $translator->trans('help.updated'));

      // Forward to show
      return $this->redirectToRoute('app_help_index');
    }

    return $this->render('help/edit.html.twig', [
      'form' => $form,
    ]);
  }
}
