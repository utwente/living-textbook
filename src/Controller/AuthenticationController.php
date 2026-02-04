<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\NewPasswordType;
use App\Form\Type\SaveType;
use App\Form\User\AddFallbackUserType;
use App\Repository\UserProtoRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Drenso\OidcBundle\Exception\OidcCodeChallengeMethodNotSupportedException;
use Drenso\OidcBundle\Exception\OidcConfigurationException;
use Drenso\OidcBundle\Exception\OidcConfigurationResolveException;
use Drenso\OidcBundle\OidcClientInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

use function bin2hex;
use function random_bytes;
use function sha1;

class AuthenticationController extends AbstractController
{
  private const string RESET_PASSWORD_USER  = '_ltb_rpu';
  private const string RESET_PASSWORD_VALID = '_ltb_rpv';

  /**
   * This route handles every login request
   * Only this route is listened to by the security services, so another route is not possible.
   *
   * This route is defined in the routes.yml in order to remove the _locale requirement
   */
  #[Route('/login_check', name: 'login_check', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function checkLogin(): Response
  {
    if ($this->isGranted('ROLE_USER')) {
      return $this->redirect($this->generateUrl('_home'));
    }

    return $this->redirect($this->generateUrl('login'));
  }

  /**
   * This controller render the default login page, which shows the option to login with SURFconext
   * or with a local account.
   */
  #[Route('/login', name: 'login', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function login(): RedirectResponse
  {
    // Forward to landing for urls backwards compatibility
    return $this->redirectToRoute('app_default_landing');
  }

  /**
   * Reset a local user password. Steps:
   * 1. Enter email address
   * 2. Reset code is sent to email
   * 3. Reset link is clicked
   * 4. Codes are verified
   * 5. Forward to create password page
   * -- Next controller - create password
   * 5. New password can be entered
   * 6. Forward to login.
   *
   * @throws TransportExceptionInterface
   */
  #[Route('/password/reset', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function resetPassword(
    Request $request, EntityManagerInterface $em, PasswordHasherFactoryInterface $passwordHasherFactory,
    UserRepository $userRepository, MailerInterface $mailer, TranslatorInterface $translator): Response
  {
    if ($this->isGranted('ROLE_USER')) {
      return $this->redirectToRoute('app_default_landing');
    }

    // Check whether the reset code is available in the request
    if (!$request->query->has('u') || !$request->query->has('e') || !$request->query->has('r')) {
      // Present user with email form
      $form = $this->createFormBuilder()
        ->add('email', EmailType::class, [
          'label'       => 'user.emailaddress',
          'constraints' => [
            new Email(),
          ],
        ])
        ->add('submit', SaveType::class, [
          'save_label'           => 'auth.request-reset',
          'enable_save_and_list' => false,
        ])
        ->getForm();

      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $email = $form->getData()['email'];
        // Find matching user
        $user = $userRepository->findOneBy([
          'username' => $email,
          'isOidc'   => false,
        ]);

        // Only continue when the user is found, but we won't tell the user
        if ($user) {
          // Generate reset code, and store it (hashed) with the user
          $resetCode = bin2hex(random_bytes(20));

          // Retrieve encoder for user
          // We cannot use the UserPasswordEncoder as it only uses the password field
          $passwordHasher = $passwordHasherFactory->getPasswordHasher($user);

          $user
            ->setResetCode($passwordHasher->hash($resetCode, null))
            ->setResetCodeValid(new DateTime()->modify('+10 minutes'));

          // Save and send mail
          $em->flush();
          $mailer->send(
            new TemplatedEmail()
              ->to($user->getAddress())
              ->subject($translator->trans('auth.password-reset.subject', [], 'communication'))
              ->htmlTemplate('communication/auth/password_reset.html.twig')
              ->context([
                'user'       => $user->getFullName(),
                'user_id'    => $user->getId(),
                'email_hash' => sha1($user->getUserIdentifier()),
                'reset_code' => $resetCode,
              ])
          );
        }

        return $this->render('authentication/reset_email_sent.html.twig', [
          'email' => $email,
        ]);
      }

      return $this->render('authentication/reset_email.html.twig', [
        'form' => $form,
      ]);
    }

    // Retrieve data from request
    $userId    = $request->query->getInt('u', 0);
    $emailHash = $request->query->get('e', '');
    $resetCode = $request->query->get('r', '');

    // Retrieve user, and validate email
    if (!$user = $userRepository->find($userId)) {
      return $this->redirectToRoute('login');
    }
    if (sha1($user->getUserIdentifier()) !== $emailHash) {
      return $this->redirectToRoute('login');
    }

    // Verify reset code
    $passwordHasher = $passwordHasherFactory->getPasswordHasher($user);
    if ($user->getResetCode() === null || !$passwordHasher->verify($user->getResetCode(), $resetCode, null)) {
      return $this->redirectToRoute('login');
    }

    // Verify reset code valid
    if ($user->getResetCodeValid() === null || $user->getResetCodeValid() < new DateTime()) {
      $this->addFlash('error', $translator->trans('auth.reset-expired'));

      return $this->redirectToRoute('login');
    }

    // Remove the one-time reset code valid time from the user object
    // This allows for feedback when the code is valid, but this page has already been called once
    $user->setResetCodeValid(null);
    $em->flush();

    // Set information in the session
    $session = $request->getSession();
    $session->set(self::RESET_PASSWORD_USER, $user->getId());
    $session->set(self::RESET_PASSWORD_VALID, new DateTime()->modify('+10 minutes'));

    // Forward to the create password page
    return $this->redirectToRoute('app_authentication_createpassword');
  }

  /**
   * Create a password for a user.
   *
   * @throws TransportExceptionInterface
   */
  #[Route('/password/create', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function createPassword(
    Request $request, EntityManagerInterface $em, UserRepository $userRepository, MailerInterface $mailer,
    UserPasswordHasherInterface $userPasswordHasher, TranslatorInterface $translator): Response
  {
    if ($this->isGranted('ROLE_USER')) {
      return $this->redirectToRoute('app_default_landing');
    }

    $session = $request->getSession();

    // Verify a valid user is set
    if (!$session->has(self::RESET_PASSWORD_USER) || !$session->has(self::RESET_PASSWORD_VALID)) {
      return $this->redirectToRoute('login');
    }

    // Check validity
    if ($session->get(self::RESET_PASSWORD_VALID) < new DateTime()) {
      $this->addFlash('error', $translator->trans('auth.reset-expired'));

      return $this->redirectToRoute('login');
    }

    // Verify user exists
    $userId = $session->get(self::RESET_PASSWORD_USER);
    if (!$user = $userRepository->find($userId)) {
      return $this->redirectToRoute('login');
    }

    // Reset session validity, to allow errors
    $session->set(self::RESET_PASSWORD_VALID, new DateTime()->modify('+10 minutes'));

    // Create the password form
    $form = $this->createFormBuilder()
      ->add('password', NewPasswordType::class)
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => false,
      ])
      ->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Store the new password
      $user
        ->setResetCode(null)
        ->setPassword($userPasswordHasher->hashPassword($user, $form->getData()['password']));
      $em->flush();

      // Clear the session
      $session->remove(self::RESET_PASSWORD_USER);
      $session->remove(self::RESET_PASSWORD_VALID);

      // Notify user
      $this->addFlash('success', $translator->trans('auth.password-changed'));
      $mailer->send(
        new TemplatedEmail()
          ->to($user->getAddress())
          ->subject($translator->trans('auth.password-reset-success.subject', [], 'communication'))
          ->htmlTemplate('communication/auth/password_reset_success.html.twig')
          ->context([
            'user' => $user->getFullName(),
          ])
      );

      // Forward to login
      return $this->redirectToRoute('login');
    }

    return $this->render('authentication/reset_password.html.twig', [
      'form' => $form,
    ]);
  }

  /** Create a new local account. This can only be done based on invite, and the supplied password must match. */
  #[Route('/account/create', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function createAccount(
    Request $request, EntityManagerInterface $em, UserProtoRepository $userProtoRepository,
    UserPasswordHasherInterface $userPasswordHasher, TranslatorInterface $translator): Response
  {
    if ($this->isGranted('ROLE_USER')) {
      return $this->redirectToRoute('app_default_landing');
    }

    if (!$request->query->has('e') || !$request->query->has('p')) {
      return $this->redirectToRoute('login');
    }

    // Retrieve query information
    $email    = $request->query->get('e', '');
    $password = $request->query->get('p', '');

    // Retrieve user proto
    if (!$userProto = $userProtoRepository->getForEmail($email)) {
      $this->addFlash('error', $translator->trans('user.invite.not-found'));

      return $this->redirectToRoute('login');
    }

    // Validate password
    if (!$userPasswordHasher->isPasswordValid($userProto, $password)) {
      return $this->redirectToRoute('login');
    }

    // Create new User object
    $user = new User()
      ->setUsername($email);

    // Create the form
    $form = $this->createForm(AddFallbackUserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Encode the password
      $user->setPassword($userPasswordHasher->hashPassword($user, $user->getPassword()));

      // Remove proto user and save the new user
      $em->remove($userProto);
      $em->persist($user);
      $em->flush();

      $this->addFlash('success', $translator->trans('auth.create-account-success'));

      return $this->redirectToRoute('login');
    }

    return $this->render('authentication/create_account.html.twig', [
      'form' => $form,
    ]);
  }

  /**
   * This controller forward the user to the SURFconext login.
   *
   * @throws OidcConfigurationException
   * @throws OidcConfigurationResolveException
   * @throws OidcCodeChallengeMethodNotSupportedException
   */
  #[Route('/login_surf', name: 'login_surf', options: ['no_login_wrap' => true])]
  #[IsGranted(AuthenticatedVoter::PUBLIC_ACCESS)]
  public function surfconext(OidcClientInterface $oidc): Response
  {
    // Redirect to authorization @ surfconext
    return $oidc->generateAuthorizationRedirect();
  }
}
