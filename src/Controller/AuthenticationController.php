<?php

namespace App\Controller;

use App\Form\Type\SaveType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Drenso\OidcBundle\Exception\OidcConfigurationException;
use Drenso\OidcBundle\Exception\OidcConfigurationResolveException;
use Drenso\OidcBundle\OidcClient;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationController extends AbstractController
{

  private const RESET_PASSWORD_USER = '_ltb_rpu';
  private const RESET_PASSWORD_VALID = '_ltb_rpv';

  /**
   * This route handles every login request
   * Only this route is listened to by the security services, so another route is not possible
   *
   * This route is defined in the routes.yml in order to remove the _locale requirement
   *
   * @Route("/login_check", name="login_check", options={"no_login_wrap"=true})
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @return Response
   */
  public function checkLogin()
  {
    if ($this->isGranted('ROLE_USER')) {
      return $this->redirect($this->generateUrl('_home'));
    } else {
      return $this->redirect($this->generateUrl('login'));
    }
  }

  /**
   * This controller render the default login page, which shows the option to login with SURFconext
   * or with an local account.
   *
   * @Route("/login", name="login", options={"no_login_wrap"=true})
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @param Request             $request
   * @param TranslatorInterface $trans
   *
   * @return array|RedirectResponse
   */
  public function login(Request $request, TranslatorInterface $trans)
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
   * 6. Forward to login
   *
   * @Route("/password/reset", options={"no_login_wrap"=true})
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @param Request                 $request
   * @param EntityManagerInterface  $em
   * @param EncoderFactoryInterface $passwordEncoderFactory
   * @param UserRepository          $userRepository
   * @param MailerInterface         $mailer
   *
   * @param TranslatorInterface     $translator
   *
   * @return Response
   *
   * @throws TransportExceptionInterface
   * @suppress PhanTypeInvalidThrowsIsInterface
   */
  public function resetPassword(
      Request $request, EntityManagerInterface $em, EncoderFactoryInterface $passwordEncoderFactory,
      UserRepository $userRepository, MailerInterface $mailer, TranslatorInterface $translator)
  {
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
          $passwordEncoder = $passwordEncoderFactory->getEncoder($user);

          $user
              ->setResetCode($passwordEncoder->encodePassword($resetCode, NULL))
              ->setResetCodeValid((new DateTime())->modify('+10 minutes'));

          // Save and send mail
          $em->flush();
          $mailer->send(
              (new TemplatedEmail())
                  ->to($user->getAddress())
                  ->subject($translator->trans('auth.password-reset.subject', [], 'communication'))
                  ->htmlTemplate('communication/auth/password_reset.html.twig')
                  ->context([
                      'user'       => $user->getFullName(),
                      'user_id'    => $user->getId(),
                      'email_hash' => sha1($user->getUsername()),
                      'reset_code' => $resetCode,
                  ])
          );
        }

        return $this->render('authentication/reset_email_sent.html.twig', [
            'email' => $email,
        ]);

      }

      return $this->render('authentication/reset_email.html.twig', [
          'form' => $form->createView(),
      ]);
    }

    // Retrieve data from request
    $userId    = $request->query->getInt('u');
    $emailHash = $request->query->get('e');
    $resetCode = $request->query->get('r');

    // Retrieve user, and validate email
    if (!$user = $userRepository->find($userId)) {
      return $this->redirectToRoute('login');
    }
    if (sha1($user->getUsername()) !== $emailHash) {
      return $this->redirectToRoute('login');
    }

    // Verify reset code
    $passwordEncoder = $passwordEncoderFactory->getEncoder($user);
    if ($user->getResetCode() === NULL || !$passwordEncoder->isPasswordValid($user->getResetCode(), $resetCode, NULL)) {
      return $this->redirectToRoute('login');
    }

    // Verify reset code valid
    if ($user->getResetCodeValid() === NULL || $user->getResetCodeValid() < new DateTime()) {
      $this->addFlash('error', $translator->trans('auth.reset-expired'));

      return $this->redirectToRoute('login');
    }

    // Remove the one-time reset code valid time from the user object
    // This allows for feedback when the code is valid, but this page has already been called once
    $user->setResetCodeValid(NULL);
    $em->flush();

    // Set information in the session
    $session = $request->getSession();
    $session->set(self::RESET_PASSWORD_USER, $user->getId());
    $session->set(self::RESET_PASSWORD_VALID, (new DateTime())->modify('+10 minutes'));

    // Forward to the create password page
    return $this->redirectToRoute('app_authentication_createpassword');
  }

  /**
   * Create a password for a user
   *
   * @Route("/password/create", options={"no_login_wrap"=true})
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @param Request                      $request
   * @param EntityManagerInterface       $em
   * @param UserRepository               $userRepository
   * @param MailerInterface              $mailer
   * @param UserPasswordEncoderInterface $userPasswordEncoder
   * @param TranslatorInterface          $translator
   *
   * @return Response
   * @throws TransportExceptionInterface
   */
  public function createPassword(
      Request $request, EntityManagerInterface $em, UserRepository $userRepository, MailerInterface $mailer,
      UserPasswordEncoderInterface $userPasswordEncoder, TranslatorInterface $translator)
  {
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
    $session->set(self::RESET_PASSWORD_VALID, (new DateTime())->modify('+10 minutes'));

    // Create the password form
    $form = $this->createFormBuilder()
        ->add('password', RepeatedType::class, [
            'type'            => PasswordType::class,
            'constraints'     => [
              // Max length due to BCrypt, @see BCryptPasswordEncoder
                new Length(['max' => 72]),
                new PasswordStrength([
                    'minLength'   => 8,
                    'minStrength' => 4,
                    'message'     => 'user.password-too-weak',
                ]),
            ],
            'invalid_message' => 'user.password-no-match',
            'first_options'   => array('label' => 'user.password'),
            'second_options'  => array('label' => 'user.repeat-password'),
        ])
        ->add('submit', SaveType::class, [
            'enable_save_and_list' => false,
            'enable_cancel'        => false,
        ])
        ->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      // Store the new password
      $user
          ->setResetCode(NULL)
          ->setPassword($userPasswordEncoder->encodePassword($user, $form->getData()['password']));
      $em->flush();

      // Clear the session
      $session->remove(self::RESET_PASSWORD_USER);
      $session->remove(self::RESET_PASSWORD_VALID);

      // Notify user
      $this->addFlash('success', $translator->trans('auth.password-changed'));
      $mailer->send(
          (new TemplatedEmail())
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
        'form' => $form->createView(),
    ]);

  }

  /**
   * This controller forward the user to the SURFconext login
   *
   * @Route("/login_surf", name="login_surf", options={"no_login_wrap"=true})
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @param SessionInterface $session
   * @param OidcClient       $oidc
   *
   * @return RedirectResponse
   *
   * @throws OidcConfigurationException
   * @throws OidcConfigurationResolveException
   */
  public function surfconext(SessionInterface $session, OidcClient $oidc)
  {
    // Remove errors from state
    $session->remove(Security::AUTHENTICATION_ERROR);
    $session->remove(Security::LAST_USERNAME);

    // Redirect to authorization @ surfconext
    return $oidc->generateAuthorizationRedirect();
  }
}
