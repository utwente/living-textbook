<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Permission\AddAdminType;
use App\Form\Type\RemoveType;
use App\Repository\UserRepository;
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
 * Class PermissionsController
 * This controller is used to edit the permissions
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/permissions", requirements={"_studyArea"="\d+"})
 */
class PermissionsController extends Controller
{

  /**
   * @Route("/admins")
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param UserRepository $userRepository
   *
   * @return array
   */
  public function admins(UserRepository $userRepository)
  {
    return [
        'admins' => $userRepository->getSuperAdmins(),
    ];
  }

  /**
   * @Route("/admin/add")
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param Request                $request
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function addAdmin(Request $request, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $form = $this->createForm(AddAdminType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $newAdmin = $form->getData()['admin'];
      assert($newAdmin instanceof User);

      $newAdmin->setIsAdmin(true);
      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.admin-added', ['%user%' => $newAdmin->getDisplayName()]));

      return $this->redirectToRoute('app_permissions_admins');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/admin/{user}/remove", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @param Request                $request
   * @param User                   $user
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   */
  public function removeAdmin(Request $request, User $user, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Check if not self
    if ($user->getId() === $this->getUser()->getId()) {
      $this->addFlash('warning', $trans->trans('permissions.cannot-edit-self', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_permissions_admins');
    }

    // Check if the user is actually an admin user
    if (!$user->isAdmin()) {
      $this->addFlash('warning', $trans->trans('permissions.no-admin', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_permissions_admins');
    }

    // Create form
    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_permissions_admins',
    ]);
    $form->handleRequest($request);

    // Handle form
    if (RemoveType::isRemoveClicked($form)) {
      $user->setIsAdmin(false);
      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.admin-removed', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_permissions_admins');
    }

    return [
        'admin' => $user,
        'form'  => $form->createView(),
    ];
  }

  /**
   * @Route("/studyarea")
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param RequestStudyArea $requestStudyArea
   *
   * @return array
   */
  public function studyArea(RequestStudyArea $requestStudyArea)
  {
    return [
        'studyArea' => $requestStudyArea->getStudyArea(),
    ];
  }

}
