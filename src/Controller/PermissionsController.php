<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Form\Permission\AddAdminType;
use App\Form\Permission\AddPermissionsType;
use App\Form\Type\RemoveType;
use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
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

  /**
   * @Route("/studyarea/add/{groupType}",requirements={"groupType"="viewer|editor|reviewer"})
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param string                 $groupType
   * @param EntityManagerInterface $em
   * @param UserGroupRepository    $userGroupRepository
   * @param UserRepository         $userRepository
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   * @throws NonUniqueResultException
   */
  public function addPermissions(Request $request, RequestStudyArea $requestStudyArea, string $groupType,
                                 EntityManagerInterface $em, UserGroupRepository $userGroupRepository, UserRepository $userRepository, TranslatorInterface $trans)
  {
    $userGroup = $userGroupRepository->getForType($requestStudyArea->getStudyArea(), $groupType);
    if (!$userGroup) {
      // Create a new group if not found
      $userGroup = (new UserGroup())->setStudyArea($requestStudyArea->getStudyArea())->setGroupType($groupType);
    }

    // Check whether there are actually users available
    $availableUserCount = $userRepository->getAvailableUsersForUserGroupQueryBuilder($userGroup)
        ->select('COUNT(u.id)')->getQuery()->getSingleScalarResult();
    if ($availableUserCount == 0) {
      $this->addFlash('notice', $trans->trans('permissions.no-users-available', ['%type%' => $groupType]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    $form = $this->createForm(AddPermissionsType::class, NULL, [
        'user_group' => $userGroup,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Persist the selected users
      foreach ($form->getData()['users'] as $user) {
        $userGroup->addUser($user);
      }
      $em->persist($userGroup);
      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.permissions-added', [
          '%type%' => $groupType,
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return [
        'studyArea' => $requestStudyArea->getStudyArea(),
        'type'      => $groupType,
        'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/studyarea/revoke/all/{groupType}",requirements={"groupType"="viewer|editor|reviewer"})
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param string                 $groupType
   * @param EntityManagerInterface $em
   * @param UserGroupRepository    $userGroupRepository
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   *
   * @throws NonUniqueResultException
   */
  public function removeAllPermissions(Request $request, RequestStudyArea $requestStudyArea, string $groupType,
                                       EntityManagerInterface $em, UserGroupRepository $userGroupRepository, TranslatorInterface $trans)
  {
    $userGroup = $userGroupRepository->getForType($requestStudyArea->getStudyArea(), $groupType);
    if (!$userGroup || $userGroup->getUsers()->isEmpty()) {
      $this->addFlash('notice', $trans->trans('permissions.remove-all-not-necessary', [
          '%type%' => $groupType,
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_permissions_studyarea',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $userGroup->getUsers()->clear();
      $em->flush();

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return [
        'studyArea' => $requestStudyArea->getStudyArea(),
        'type'      => $groupType,
        'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/studyarea/revoke/{user}/{groupType}",requirements={"user"="\d+", "groupType"="viewer|editor|reviewer"})
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param string                 $groupType
   * @param User                   $user
   * @param EntityManagerInterface $em
   * @param UserGroupRepository    $userGroupRepository
   * @param TranslatorInterface    $trans
   *
   * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
   *
   * @throws NonUniqueResultException
   */
  public function removePermission(Request $request, RequestStudyArea $requestStudyArea, string $groupType, User $user,
                                   EntityManagerInterface $em, UserGroupRepository $userGroupRepository, TranslatorInterface $trans)
  {
    // Retrieve the correct user group
    $userGroup = $userGroupRepository->getForType($requestStudyArea->getStudyArea(), $groupType);
    if (!$userGroup || !$userGroup->getUsers()->contains($user)) {
      $this->addFlash('notice', $trans->trans('permissions.remove-not-necessary', [
          '%type%' => $groupType, '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_permissions_studyarea',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $userGroup->removeUser($user);
      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.removed-single-permission', [
          '%type%' => $groupType, '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return [
        'studyArea' => $requestStudyArea->getStudyArea(),
        'user'      => $user,
        'type'      => $groupType,
        'form'      => $form->createView(),
    ];
  }

}
