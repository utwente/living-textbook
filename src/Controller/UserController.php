<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserApiToken;
use App\Entity\UserProto;
use App\Form\Type\RemoveType;
use App\Form\User\AddFallbackUsersType;
use App\Form\User\ChangePasswordType;
use App\Form\User\EditFallbackUserType;
use App\Form\User\GenerateApiTokenType;
use App\Form\User\UpdatePasswordType;
use App\Repository\StudyAreaRepository;
use App\Repository\UserApiTokenRepository;
use App\Repository\UserProtoRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Random\RandomException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

use function assert;
use function bin2hex;
use function random_bytes;
use function sprintf;

#[Route('/{_studyArea<\d+>}/users')]
class UserController extends AbstractController
{
  #[Route('/api-tokens')]
  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
  public function apiTokens(UserApiTokenRepository $tokenRepository): Response
  {
    return $this->render('user/api_tokens.html.twig', [
      'tokens' => $tokenRepository->findBy(['user' => $this->getUser()]),
    ]);
  }

  #[Route('/api-tokens/generate')]
  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
  public function apiTokensGenerate(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher): Response
  {
    $user = $this->getUser();
    assert($user instanceof User);
    $formToken = new UserApiToken($user, '');
    $form      = $this->createForm(GenerateApiTokenType::class, $formToken);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Create the actual token, with the actual token in it
      $token       = bin2hex(random_bytes(32));
      $tokenObject = new UserApiToken($user, $passwordHasher->hashPassword($formToken, $token))
        ->setDescription($formToken->getDescription())
        ->setValidUntil($formToken->getValidUntil());

      $em->persist($tokenObject);
      $em->flush();

      $request->getSession()->set('new_token', sprintf('%s_%s', $tokenObject->getTokenId(), $token));

      return $this->redirectToRoute('app_user_apitokensgenerated');
    }

    return $this->render('user/api_tokens_generate.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/api-tokens/generated')]
  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
  public function apiTokensGenerated(Request $request): Response
  {
    if (!$token = $request->getSession()->get('new_token')) {
      return $this->redirectToRoute('app_user_apitokens');
    }

    // Clear token from session
    $request->getSession()->remove('new_token');

    return $this->render('user/api_tokens_show.html.twig', [
      'token' => $token,
    ]);
  }

  #[Route('/api-tokens/remove/{userApiToken<\d+>}')]
  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
  public function apiTokensRemove(
    Request $request,
    UserApiToken $userApiToken,
    EntityManagerInterface $em,
    TranslatorInterface $trans): Response
  {
    $currentUser = $this->getUser();
    assert($currentUser instanceof User);
    if ($userApiToken->getUser()->getId() !== $currentUser->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_user_apitokens',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $em->remove($userApiToken);
      $em->flush();

      $this->addFlash('success', $trans->trans('user.api-tokens.removed'));

      return $this->redirectToRoute('app_user_apitokens');
    }

    return $this->render('user/api_tokens_remove.html.twig', [
      'form' => $form,
    ]);
  }

  /** @throws TransportExceptionInterface|RandomException */
  #[Route('/fallback/add')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function fallbackAdd(
    Request $request,
    EntityManagerInterface $em,
    TranslatorInterface $trans,
    MailerInterface $mailer,
    UserRepository $userRepository,
    UserProtoRepository $userProtoRepository,
    UserPasswordHasherInterface $passwordHasher): Response
  {
    $form = $this->createForm(AddFallbackUsersType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $notificationContexts = [];

      // Create the user proto object, and send mails
      foreach ($form->getData()['emails'] as $email) {
        // Verify whether we already know this email address
        if ($userProtoRepository->getForEmail($email)
            || $userRepository->getUserForEmail($email)) {
          continue;
        }

        // Create the proto user
        $userProto = new UserProto()
          ->setEmail($email);

        // Generate a password
        $password = bin2hex(random_bytes(20));
        $userProto->setPassword($passwordHasher->hashPassword($userProto, $password));

        // Persist the new user
        $em->persist($userProto);
        $notificationContexts[] = [
          'user_email' => $email,
          'password'   => $password,
        ];
      }

      // Save the new user
      $em->flush();

      // Schedule emails
      foreach ($notificationContexts as $notificationContext) {
        $mailer->send(
          new TemplatedEmail()
            ->to($notificationContext['user_email'])
            ->subject($trans->trans('auth.new-local-account.subject', [], 'communication'))
            ->htmlTemplate('communication/auth/new_local_account.html.twig')
            ->context($notificationContext)
        );
      }

      $this->addFlash('success', $trans->trans('user.fallback.added'));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return $this->render('user/fallback_add.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route('/fallback/password/change')]
  #[IsGranted(AuthenticatedVoter::IS_AUTHENTICATED_FULLY)]
  public function fallbackChangeOwnPassword(
    Request $request,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher,
    TranslatorInterface $trans): Response
  {
    $user = $this->getUser();
    assert($user instanceof User);

    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(ChangePasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $user->setPassword($passwordHasher->hashPassword($user, $form->getData()['password']));
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.password-updated-own'));

      return $this->redirectToRoute('app_default_dashboard');
    }

    return $this->render('user/fallback_change_own_password.html.twig', [
      'form' => $form,
    ]);
  }

  #[Route(path: '/fallback/edit/{user<\d+>}', requirements: ['user' => '\d+'])]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function fallbackEdit(Request $request, User $user, EntityManagerInterface $em, TranslatorInterface $trans): Response
  {
    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(EditFallbackUserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.updated', ['%user%' => $user->getDisplayName()]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return $this->render('user/fallback_edit.html.twig', [
      'user' => $user,
      'form' => $form,
    ]);
  }

  #[Route('/fallback/list')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function fallbackList(UserRepository $userRepository, UserProtoRepository $userProtoRepository): Response
  {
    // Retrieve users
    return $this->render('user/fallback_list.html.twig', [
      'users'        => $userRepository->getFallbackUsers(),
      'open_invites' => $userProtoRepository->findAll(),
    ]);
  }

  #[Route('/fallback/password/{user<\d+>}')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function fallbackResetPassword(
    Request $request,
    User $user,
    EntityManagerInterface $em,
    UserPasswordHasherInterface $passwordHasher,
    TranslatorInterface $trans): Response
  {
    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(UpdatePasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $user->setPassword($passwordHasher->hashPassword($user, $form->getData()['password']));
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.password-updated', [
        '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    $this->addFlash('warning', $trans->trans('user.fallback.reset-password-warning'));

    return $this->render('user/fallback_reset_password.html.twig', [
      'user' => $user,
      'form' => $form,
    ]);
  }

  #[Route('/fallback/remove/{user<\d+>}')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function fallbackRemove(
    Request $request,
    User $user,
    EntityManagerInterface $em,
    TranslatorInterface $trans,
    StudyAreaRepository $studyAreaRepository): Response
  {
    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    // Check if self
    $secUser = $this->getUser();
    assert($secUser instanceof User);
    if ($user->getId() == $secUser->getId()) {
      $this->addFlash('notice', $trans->trans('user.fallback.self-remove'));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    // Get owned study areas
    $studyAreas = $studyAreaRepository->findBy(['owner' => $user], ['name' => 'ASC']);

    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_user_fallbacklist',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      foreach ($studyAreas as $studyArea) {
        $em->remove($studyArea);
      }
      $em->remove($user);
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.removed', [
        '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return $this->render('user/fallback_remove.html.twig', [
      'user'       => $user,
      'studyAreas' => $studyAreas,
      'form'       => $form,
    ]);
  }

  #[Route('/invite/remove/{user<\d+>}')]
  #[IsGranted(User::ROLE_SUPER_ADMIN)]
  public function fallbackInviteRemove(
    Request $request,
    UserProto $user,
    EntityManagerInterface $em,
    TranslatorInterface $trans): Response
  {
    $form = $this->createForm(RemoveType::class, null, [
      'cancel_route' => 'app_user_fallbacklist',
    ]);
    $form->handleRequest($request);

    if (RemoveType::isRemoveClicked($form)) {
      $em->remove($user);
      $em->flush();

      $this->addFlash('success', $trans->trans('user.invite.removed', [
        '%email%' => $user->getEmail(),
      ]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return $this->render('user/fallback_invite_remove.html.twig', [
      'userProto' => $user,
      'form'      => $form,
    ]);
  }
}
