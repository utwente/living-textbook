<?php

namespace App\Controller;

use App\Form\Authentication\LoginType;
use Drenso\OidcBundle\OidcClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthenticationController extends AbstractController
{

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
   * @Template
   * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
   *
   * @param Request             $request
   * @param TranslatorInterface $trans
   *
   * @return array|RedirectResponse
   */
  public function login(Request $request, TranslatorInterface $trans)
  {
    if ($this->isGranted('ROLE_USER')) {
      return $this->redirectToRoute('_home');
    }

    $session = $request->getSession();
    $form    = $this->createForm(LoginType::class, array(
        '_username' => $session->get(Security::LAST_USERNAME, ''),
    ), array(
        'action' => $this->generateUrl('login_check'),
    ));

    if ($session->has(Security::AUTHENTICATION_ERROR)) {
      // Retrieve the error and remove it from the session
      $authError = $session->get(Security::AUTHENTICATION_ERROR);
      $session->remove(Security::AUTHENTICATION_ERROR);

      // Check the actual error
      if ($authError instanceof BadCredentialsException) {
        // Bad credentials given
        $this->addFlash('authError', $trans->trans('login.bad-credentials'));
      } else {
        // General error occurred
        $this->addFlash('authError', $trans->trans('login.general-error'));
      }
    }

    return [
        'form'       => $form->createView(),
        'formActive' => $session->get(Security::LAST_USERNAME, '') !== '',
    ];
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
   * @throws \Drenso\OidcBundle\Exception\OidcConfigurationException
   * @throws \Drenso\OidcBundle\Exception\OidcConfigurationResolveException
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
