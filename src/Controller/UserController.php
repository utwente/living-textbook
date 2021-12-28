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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController
 *
 * @Route("/{_studyArea}/users", requirements={"_studyArea"="\d+"})
 */
class UserController extends AbstractController
{

  /**
   * @Route("/api-tokens")
   * @IsGranted("ROLE_USER")
   */
  public function apiTokens(UserApiTokenRepository $tokenRepository): Response
  {
    return $this->render('user/api_tokens.html.twig', [
        'tokens' => $tokenRepository->findBy(['user' => $this->getUser()]),
    ]);
  }

  /**
   * @Route("/api-tokens/generate")
   * @IsGranted("ROLE_USER")
   */
  public function apiTokensGenerate(
      Request                      $request,
      EntityManagerInterface       $em,
      UserPasswordEncoderInterface $passwordEncoder): Response
  {
    $user = $this->getUser();
    assert($user instanceof User);
    $formToken = new UserApiToken($user, '');
    $form      = $this->createForm(GenerateApiTokenType::class, $formToken);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Create the actual token, with the actual token in it
      $token       = bin2hex(random_bytes(32));
      $tokenObject = (new UserApiToken($user, $passwordEncoder->encodePassword($formToken, $token)))
          ->setDescription($formToken->getDescription())
          ->setValidUntil($formToken->getValidUntil());

      $em->persist($tokenObject);
      $em->flush();

      $request->getSession()->set('new_token', sprintf('%s_%s', $tokenObject->getTokenId(), $token));

      return $this->redirectToRoute('app_user_apitokensgenerated');
    }

    return $this->render('user/api_tokens_generate.html.twig', [
        'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/api-tokens/generated")
   * @IsGranted("ROLE_USER")
   */
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

  /**
   * @Route("/api-tokens/remove/{userApiToken}", requirements={"userApiToken": "\d+"})
   * @IsGranted("ROLE_USER")
   */
  public function apiTokensRemove(
      Request                $request,
      UserApiToken           $userApiToken,
      EntityManagerInterface $em,
      TranslatorInterface    $trans): Response
  {
    $currentUser = $this->getUser();
    assert($currentUser instanceof User);
    if ($userApiToken->getUser()->getId() !== $currentUser->getId()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(RemoveType::class, NULL, [
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
        'form' => $form->createView(),
    ]);
  }

  /**
   * @Route("/fallback/add")
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @return array|Response
   * @throws TransportExceptionInterface
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function fallbackAdd(
      Request                      $request,
      EntityManagerInterface       $em,
      TranslatorInterface          $trans,
      MailerInterface              $mailer,
      UserRepository               $userRepository,
      UserProtoRepository          $userProtoRepository,
      UserPasswordEncoderInterface $userPasswordEncoder)
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
        $userProto = (new UserProto())
            ->setEmail($email);

        // Generate a password
        $password = bin2hex(random_bytes(20));
        $userProto->setPassword($userPasswordEncoder->encodePassword($userProto, $password));

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
            (new TemplatedEmail())
                ->to($notificationContext['user_email'])
                ->subject($trans->trans('auth.new-local-account.subject', [], 'communication'))
                ->htmlTemplate('communication/auth/new_local_account.html.twig')
                ->context($notificationContext)
        );
      }

      $this->addFlash('success', $trans->trans('user.fallback.added'));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/fallback/password/change")
   * @Template()
   * @IsGranted("ROLE_USER")
   *
   * @return array|Response
   */
  public function fallbackChangeOwnPassword(
      Request                      $request,
      EntityManagerInterface       $em,
      UserPasswordEncoderInterface $encoder,
      TranslatorInterface          $trans)
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
      $user->setPassword($encoder->encodePassword($user, $form->getData()['password']));
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.password-updated-own'));

      return $this->redirectToRoute('app_default_dashboard');
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/fallback/edit/{user}", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @return array|Response
   */
  public function fallbackEdit(Request $request, User $user, EntityManagerInterface $em, TranslatorInterface $trans)
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

    return [
        'user' => $user,
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/fallback/list")
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   */
  public function fallbackList(UserRepository $userRepository, UserProtoRepository $userProtoRepository): array
  {
    // Retrieve users
    return [
        'users'        => $userRepository->getFallbackUsers(),
        'open_invites' => $userProtoRepository->findAll(),
    ];
  }

  /**
   * @Route("/fallback/password/{user}", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @return array|Response
   */
  public function fallbackResetPassword(
      Request                      $request,
      User                         $user,
      EntityManagerInterface       $em,
      UserPasswordEncoderInterface $encoder,
      TranslatorInterface          $trans)
  {
    // Check whether user is a fallback user
    if ($user->isOidc()) {
      throw $this->createNotFoundException();
    }

    $form = $this->createForm(UpdatePasswordType::class);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $user->setPassword($encoder->encodePassword($user, $form->getData()['password']));
      $em->flush();

      $this->addFlash('success', $trans->trans('user.fallback.password-updated', [
          '%user%' => $user->getDisplayName(),
      ]));

      return $this->redirectToRoute('app_user_fallbacklist');
    }

    $this->addFlash('warning', $trans->trans('user.fallback.reset-password-warning'));

    return [
        'user' => $user,
        'form' => $form->createView(),
    ];
  }

  /**
   * @Route("/fallback/remove/{user}", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @return array|Response
   */
  public function fallbackRemove(
      Request                $request,
      User                   $user,
      EntityManagerInterface $em,
      TranslatorInterface    $trans,
      StudyAreaRepository    $studyAreaRepository)
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

    $form = $this->createForm(RemoveType::class, NULL, [
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

    return [
        'user'       => $user,
        'studyAreas' => $studyAreas,
        'form'       => $form->createView(),
    ];
  }

  /**
   * @Route("/invite/remove/{user}", requirements={"user"="\d+"})
   * @Template()
   * @IsGranted("ROLE_SUPER_ADMIN")
   *
   * @return array|Response
   */
  public function fallbackInviteRemove(
      Request                $request,
      UserProto              $user,
      EntityManagerInterface $em,
      TranslatorInterface    $trans)
  {
    $form = $this->createForm(RemoveType::class, NULL, [
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

    return [
        'userProto' => $user,
        'form'      => $form->createView(),
    ];
  }

}
