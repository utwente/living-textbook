<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupEmail;
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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PermissionsController
 * This controller is used to edit the permissions
 *
 * @author BobV
 *
 * @Route("/{_studyArea}/permissions", requirements={"_studyArea"="\d+"})
 */
class PermissionsController extends AbstractController
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
   * @Route("/studyarea/add")
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param EntityManagerInterface $em
   * @param UserGroupRepository    $userGroupRepository
   * @param UserRepository         $userRepository
   * @param TranslatorInterface    $trans
   *
   * @return array|Response
   * @throws NonUniqueResultException
   */
  public function addPermissions(
      Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em,
      UserGroupRepository $userGroupRepository, UserRepository $userRepository, TranslatorInterface $trans)
  {
    $studyArea  = $requestStudyArea->getStudyArea();
    $groupTypes = $studyArea->getAvailableUserGroupTypes();
    $form       = $this->createForm(AddPermissionsType::class, NULL, [
        'group_types' => $groupTypes,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

      // Parse the email addresses, and convert them to users if applicable
      $formData = $form->getData();
      $emails   = $formData['emails'];

      // Find users, and remove those from the email list
      $foundUsers = $userRepository->getUsersForEmails($emails);
      $emails     = array_diff($emails, array_map(function (User $foundUser) {
        return $foundUser->getUsername();
      }, $foundUsers));

      // Add the users/emails to the requested groups
      foreach ($groupTypes as $groupType) {
        // Skip if the group is not requested
        if (!$formData['permissions'][$groupType]) {
          continue;
        }

        // Retrieve or create the user group
        $userGroup = $userGroupRepository->getForType($studyArea, $groupType)
            ?? (new UserGroup())->setStudyArea($studyArea)->setGroupType($groupType);

        // Add the users
        foreach ($foundUsers as $foundUser) {
          $userGroup->addUser($foundUser);
        }
        foreach ($emails as $email) {
          $userGroup->addEmail($email);
        }

        $em->persist($userGroup);
      }

      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.permissions-added'));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return [
        'studyArea' => $studyArea,
        'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/studyarea/revoke/all/{groupType}", requirements={"groupType"="viewer|editor|reviewer|analysis"})
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
  public function removeAllPermissions(
      Request $request, RequestStudyArea $requestStudyArea, string $groupType,
      EntityManagerInterface $em, UserGroupRepository $userGroupRepository, TranslatorInterface $trans)
  {
    $userGroup = $userGroupRepository->getForType($requestStudyArea->getStudyArea(), $groupType);
    if (!$userGroup || ($userGroup->getUsers()->isEmpty() && $userGroup->getEmails()->isEmpty())) {
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
      foreach ($userGroup->getEmails() as $userGroupEmail) {
        $em->remove($userGroupEmail);
      }
      $userGroup->getEmails()->clear();
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
   * @Route("/studyarea/revoke/self")
   * @Template
   * @IsGranted("STUDYAREA_SHOW", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param TranslatorInterface    $translator
   * @param EntityManagerInterface $entityManager
   *
   * @return array|RedirectResponse
   */
  public function removeSelf(
      Request $request, RequestStudyArea $requestStudyArea, TranslatorInterface $translator,
      EntityManagerInterface $entityManager)
  {
    $user = $this->getUser();
    if (!$user) {
      // This page does not exist for anonymous users
      throw $this->createNotFoundException();
    }
    assert($user instanceof User);

    // Not allowed for study area owners
    $studyArea = $requestStudyArea->getStudyArea();
    if ($this->isGranted('STUDYAREA_OWNER', $studyArea)) {
      $this->addFlash('notice', $translator->trans('permissions.cannot-remove-self'));

      return $this->redirectToRoute('app_default_dashboard');
    }

    $form = $this->createForm(RemoveType::class, NULL, [
        'remove_label' => 'permissions.remove-self-confirm',
        'cancel_route' => 'app_default_dashboard',
    ]);

    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      foreach ($studyArea->getUserGroups() as $userGroup) {
        $userGroup->removeUser($user);
      }

      $entityManager->flush();
      $this->addFlash('notice', $translator->trans('permissions.removed-self', ['%studyArea%' => $studyArea->getName()]));

      return $this->redirectToRoute('app_default_landing');
    }

    return [
        'form'      => $form->createView(),
        'studyArea' => $studyArea,
    ];
  }

  /**
   * @Route("/studyarea/revoke/{user}", requirements={"user"="\d+"})
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param User                   $user
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|RedirectResponse
   */
  public function removePermissions(
      Request $request, RequestStudyArea $requestStudyArea, User $user, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    $studyArea = $requestStudyArea->getStudyArea();

    // Retrieve the user groups this user is in
    $userGroups = $studyArea->getUserGroups()->filter(function (UserGroup $userGroup) use ($user) {
      return $userGroup->getUsers()->contains($user);
    });
    if (count($userGroups) === 0) {
      $this->addFlash('notice', $trans->trans('permissions.remove-not-possible', [
          '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    // Create form
    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_permissions_studyarea',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      foreach ($userGroups as $userGroup) {
        $userGroup->removeUser($user);
      }

      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.removed-permissions', [
          '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return [
        'studyArea' => $studyArea,
        'user'      => $user,
        'form'      => $form->createView(),
    ];
  }

  /**
   * @Route("/studyarea/revoke/{email}")
   * @Template
   * @IsGranted("STUDYAREA_OWNER", subject="requestStudyArea")
   *
   * @param Request                $request
   * @param RequestStudyArea       $requestStudyArea
   * @param string                 $email
   * @param EntityManagerInterface $em
   * @param TranslatorInterface    $trans
   *
   * @return array|RedirectResponse
   */
  public function removeEmailPermissions(
      Request $request, RequestStudyArea $requestStudyArea, string $email, EntityManagerInterface $em, TranslatorInterface $trans)
  {
    // Decode email
    $email     = urldecode($email);
    $studyArea = $requestStudyArea->getStudyArea();

    // Retrieve the correct user group
    $userGroupEmails = [];
    foreach ($studyArea->getUserGroups() as $userGroup) {
      $userGroupEmails = array_merge($userGroupEmails, $userGroup->getEmails()->filter(function (UserGroupEmail $userGroup) use ($email) {
        return $userGroup->getEmail() == mb_strtolower(trim($email));
      })->toArray());
    }

    // Verify whether remove is required
    if (count($userGroupEmails) === 0) {
      $this->addFlash('notice', $trans->trans('permissions.remove-not-possible', [
          '%user%' => $email,
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    // Create form
    $form = $this->createForm(RemoveType::class, NULL, [
        'cancel_route' => 'app_permissions_studyarea',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      foreach ($userGroupEmails as $userGroupEmail) {
        $em->remove($userGroupEmail);
      }

      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.removed-permissions', [
          '%user%' => $email,
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return [
        'studyArea' => $studyArea,
        'email'     => $email,
        'form'      => $form->createView(),
    ];
  }

}
