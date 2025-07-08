<?php

namespace App\Controller;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupEmail;
use App\Form\Permission\AddAdminType;
use App\Form\Permission\AddPermissionsType;
use App\Form\Type\RemoveType;
use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use App\Request\Wrapper\RequestStudyArea;
use App\Security\UserPermissions;
use App\Security\Voters\StudyAreaVoter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use function array_diff;
use function array_filter;
use function array_map;
use function array_merge;
use function assert;
use function count;
use function mb_strtolower;
use function trim;
use function urldecode;

#[Route('/{_studyArea<\d+>}/permissions')]
class PermissionsController extends AbstractController
{
  #[Route('/admins')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function admins(UserRepository $userRepository): Response
  {
    return $this->render('permissions/admins.html.twig', [
      'admins' => $userRepository->getSuperAdmins(),
    ]);
  }

  #[Route('/admin/add')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function addAdmin(Request $request, EntityManagerInterface $em, TranslatorInterface $trans): Response
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

    return $this->render('permissions/add_admin.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route(path: '/admin/{user<\d+>}/remove')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function removeAdmin(Request $request, User $user, EntityManagerInterface $em, TranslatorInterface $trans): Response
  {
    // Check if not self
    $secUser = $this->getUser();
    assert($secUser instanceof User);
    if ($user->getId() === $secUser->getId()) {
      $this->addFlash('warning', $trans->trans('permissions.cannot-edit-self', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_permissions_admins');
    }

    // Check if the user is actually an admin user
    if (!$user->isAdmin()) {
      $this->addFlash('warning', $trans->trans('permissions.no-admin', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_permissions_admins');
    }

    // Create form
    $form = $this->createForm(RemoveType::class, null, [
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

    return $this->render('permissions/remove_admin.html.twig', [
      'admin' => $user,
      'form'  => $form,
    ]);
  }

  #[Route('/studyarea')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function studyArea(RequestStudyArea $requestStudyArea): Response
  {
    return $this->render('permissions/study_area.html.twig', [
      'studyArea' => $requestStudyArea->getStudyArea(),
    ]);
  }

  /** @throws NonUniqueResultException */
  #[Route('/studyarea/add')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function addPermissions(
    Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em,
    UserGroupRepository $userGroupRepository, UserRepository $userRepository, TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    if ($studyArea->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      return $this->redirectToRoute('app_permissions_studyarea');
    }

    $groupTypes = $studyArea->getAvailableUserGroupTypes();
    $form       = $this->createForm(AddPermissionsType::class, null, [
      'group_types' => $groupTypes,
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Parse the email addresses, and convert them to users if applicable
      $formData = $form->getData();
      $emails   = $formData['emails'];

      // Find users, and remove those from the email list
      $foundUsers = $userRepository->getUsersForEmails($emails);
      $emails     = array_diff($emails, array_map(fn (User $foundUser) => $foundUser->getUserIdentifier(), $foundUsers));

      // Add the users/emails to the requested groups
      foreach ($groupTypes as $groupType) {
        // Skip if the group is not requested
        if (!$formData['permissions'][$groupType]) {
          continue;
        }

        // Retrieve or create the user group
        $userGroup = $userGroupRepository->getForType($studyArea, $groupType)
            ?? new UserGroup()->setStudyArea($studyArea)->setGroupType($groupType);

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

    return $this->render('permissions/add_permissions.html.twig', [
      'studyArea' => $studyArea,
      'form'      => $form,
    ]);
  }

  /** @throws NonUniqueResultException */
  #[Route('/studyarea/update/{user<\d+>}/{groupType<editor|reviewer|analysis>}', options: ['expose' => true], methods: [Request::METHOD_POST])]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function updatePermission(
    Request $request, User $user, string $groupType, EntityManagerInterface $em, RequestStudyArea $requestStudyArea,
    UserGroupRepository $userGroupRepository): JsonResponse
  {
    $studyArea = $requestStudyArea->getStudyArea();
    if ($studyArea->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      throw new BadRequestHttpException('Cannot update private study area permissions');
    }

    $userGroup = $userGroupRepository->getForType($studyArea, $groupType)
        ?? new UserGroup()->setStudyArea($studyArea)->setGroupType($groupType);

    $newPermission = null;
    if ($userGroup->getUsers()->contains($user)) {
      $userGroup->removeUser($user);
      $newPermission = 0;
    } else {
      $userGroup->addUser($user);
      $newPermission = 1;
    }

    $em->persist($userGroup);
    $em->flush();

    return new JsonResponse([
      'value' => $newPermission,
    ]);
  }

  /** @throws NonUniqueResultException */
  #[Route('/studyarea/update/{email}/{groupType<editor|reviewer|analysis>}', options: ['expose' => true], methods: [Request::METHOD_POST])]
  #[IsGranted('STUDYAREA_OWNER', subject: 'requestStudyArea')]
  public function updateEmailPermission(
    Request $request, string $email, string $groupType, EntityManagerInterface $em, RequestStudyArea $requestStudyArea,
    UserGroupRepository $userGroupRepository): JsonResponse
  {
    $studyArea = $requestStudyArea->getStudyArea();
    if ($studyArea->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      throw new BadRequestHttpException('Cannot update private study area permissions');
    }

    $userGroup = $userGroupRepository->getForType($studyArea, $groupType)
        ?? new UserGroup()->setStudyArea($studyArea)->setGroupType($groupType);

    $email = mb_strtolower(trim(urldecode($email)));

    $newPermission   = null;
    $userGroupEmails = $userGroup->getEmails()->filter(fn (UserGroupEmail $userGroupEmail) => $userGroupEmail->getEmail() === $email);

    if (count($userGroupEmails) > 0) {
      foreach ($userGroupEmails as $userGroupEmail) {
        $em->remove($userGroupEmail);
      }
      $newPermission = 0;
    } else {
      $userGroup->addEmail($email);
      $newPermission = 1;

      // Only call persist when there is a new group
      $em->persist($userGroup);
    }

    $em->flush();

    return new JsonResponse([
      'value' => $newPermission,
    ]);
  }

  #[Route('/studyarea/revoke/all')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function removeAllPermissions(
    Request $request, RequestStudyArea $requestStudyArea, EntityManagerInterface $em, TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    if ($studyArea->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      return $this->redirectToRoute('app_permissions_studyarea');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_permissions_studyarea',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      foreach ($studyArea->getUserGroups() as $userGroup) {
        $em->remove($userGroup);
      }
      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.removed-permissions-all'));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return $this->render('permissions/remove_all_permissions.html.twig', [
      'studyArea' => $studyArea,
      'form'      => $form,
    ]);
  }

  /** @throws NonUniqueResultException */
  #[Route('/studyarea/revoke/all/{groupType<viewer|editor|reviewer|analysis>}')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function removeAllPermissionsForType(
    Request $request, RequestStudyArea $requestStudyArea, string $groupType,
    EntityManagerInterface $em, UserGroupRepository $userGroupRepository, TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    if ($studyArea->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      return $this->redirectToRoute('app_permissions_studyarea');
    }

    $userGroup       = $userGroupRepository->getForType($studyArea, $groupType);
    $userPermissions = $studyArea->getUserPermissions();

    if ($groupType === UserGroup::GROUP_VIEWER) {
      $notNecessary = 0 === count(array_filter($userPermissions, fn (UserPermissions $userPermission) => $userPermission->isViewerOnly()));
    } else {
      $notNecessary = !$userGroup || ($userGroup->getUsers()->isEmpty() && $userGroup->getEmails()->isEmpty());
    }

    if ($notNecessary) {
      $this->addFlash('notice', $trans->trans('permissions.remove-all-not-necessary', [
        '%type%' => $groupType,
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_permissions_studyarea',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      if ($groupType === UserGroup::GROUP_VIEWER) {
        // Only remove users which do not have other roles assigned in the same area
        foreach ($userPermissions as $userPermission) {
          if (!$userPermission->isViewerOnly()) {
            continue;
          }

          // Remove this user from the group
          if ($userPermission->isUser()) {
            $userGroup->removeUser($userPermission->getUser());
          } else {
            $em->remove($userPermission->getEmail());
            $userGroup->removeEmail($userPermission->getEmail());
          }
        }
      } else {
        // Remove all users when it is not the viewer role
        $userGroup->getUsers()->clear();
        foreach ($userGroup->getEmails() as $userGroupEmail) {
          $em->remove($userGroupEmail);
        }
        $userGroup->getEmails()->clear();
      }

      $em->flush();

      $this->addFlash('success', $trans->trans('permissions.removed-permissions-type', [
        '%type%' => $groupType,
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    return $this->render('permissions/remove_all_permissions_for_type.html.twig', [
      'studyArea' => $studyArea,
      'type'      => $groupType,
      'form'      => $form,
    ]);
  }

  #[Route('/studyarea/revoke/self')]
  #[IsGranted(StudyAreaVoter::SHOW, subject: 'requestStudyArea')]
  public function removeSelf(
    Request $request, RequestStudyArea $requestStudyArea, TranslatorInterface $translator,
    EntityManagerInterface $entityManager): Response
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

    $form = $this->createForm(RemoveType::class, null, [
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

    return $this->render('permissions/remove_self.html.twig', [
      'form'      => $form,
      'studyArea' => $studyArea,
    ]);
  }

  #[Route('/studyarea/revoke/{user<\d+>}')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function removePermissions(
    Request $request, RequestStudyArea $requestStudyArea, User $user, EntityManagerInterface $em, TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    if ($studyArea->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      return $this->redirectToRoute('app_permissions_studyarea');
    }

    // Retrieve the user groups this user is in
    $userGroups = $studyArea->getUserGroups()->filter(fn (UserGroup $userGroup) => $userGroup->getUsers()->contains($user));
    if (count($userGroups) === 0) {
      $this->addFlash('notice', $trans->trans('permissions.remove-not-possible', [
        '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    // Create form
    $form = $this->createForm(RemoveType::class, null, [
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

    return $this->render('permissions/remove_permissions.html.twig', [
      'studyArea' => $studyArea,
      'user'      => $user,
      'form'      => $form,
    ]);
  }

  #[Route('/studyarea/revoke/{email}')]
  #[IsGranted(StudyAreaVoter::OWNER, subject: 'requestStudyArea')]
  public function removeEmailPermissions(
    Request $request, RequestStudyArea $requestStudyArea, string $email, EntityManagerInterface $em, TranslatorInterface $trans): Response
  {
    $studyArea = $requestStudyArea->getStudyArea();
    if ($studyArea->getAccessType() === StudyArea::ACCESS_PRIVATE) {
      return $this->redirectToRoute('app_permissions_studyarea');
    }

    // Decode email
    $email = mb_strtolower(trim(urldecode($email)));

    // Retrieve the correct user group
    $userGroupEmails = [];
    foreach ($studyArea->getUserGroups() as $userGroup) {
      $userGroupEmails = array_merge($userGroupEmails, $userGroup->getEmails()->filter(fn (UserGroupEmail $userGroup) => $userGroup->getEmail() == $email)->toArray());
    }

    // Verify whether remove is required
    if (count($userGroupEmails) === 0) {
      $this->addFlash('notice', $trans->trans('permissions.remove-not-possible', [
        '%user%' => $email,
      ]));

      return $this->redirectToRoute('app_permissions_studyarea');
    }

    // Create form
    $form = $this->createForm(RemoveType::class, null, [
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

    return $this->render('permissions/remove_email_permissions.html.twig', [
      'studyArea' => $studyArea,
      'email'     => $email,
      'form'      => $form,
    ]);
  }
}
