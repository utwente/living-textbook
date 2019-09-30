<?php

namespace App\Controller;

use App\Entity\Help;
use App\Form\Help\EditHelpType;
use App\Repository\HelpRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


/**
 * Class HelpController
 * Scoped per study area to prevent reload issues
 *
 * @Route("/{_studyArea}/help", requirements={"_studyArea"="\d+"})
 */
class HelpController extends AbstractController
{

  /**
   * Displays the available help documents
   *
   * @Route()
   * @Template()
   * @IsGranted("ROLE_USER")
   *
   * @param HelpRepository $helpRepository
   *
   * @return array
   *
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  public function index(HelpRepository $helpRepository)
  {
    return [
        'help' => $helpRepository->getCurrent(),
    ];
  }

  /**
   * Edit the help page
   *
   * @Route("/edit")
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param Request                $request
   * @param HelpRepository         $helpRepository
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $translator
   *
   * @return array|Response
   *
   * @throws NoResultException
   * @throws NonUniqueResultException
   */
  public function edit(
      Request $request, HelpRepository $helpRepository, EntityManagerInterface $em, TranslatorInterface $translator)
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

    return [
        'form' => $form->createView(),
    ];
  }
}
