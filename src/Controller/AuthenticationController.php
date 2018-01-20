<?php

namespace App\Controller;

use App\Form\Authentication\LoginType;
use App\Oidc\OidcClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Security;

class AuthenticationController extends Controller
{

  /**
   * This route handles every login request
   * Only this route is listened to by the security services, so another route is not possible
   *
   * This route is defined in the routes.yml in order to remove the _locale requirement
   *
   * @Route("/login_check", name="login_check")
   *
   * @return Response
   */
  public function checkLogin()
  {
    if ($this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
      return $this->redirect($this->generateUrl('app_default_index'));
    } else {
      return $this->redirect($this->generateUrl('login'));
    }
  }

  /**
   * This controller render the default login page, which shows the option to login with SURFconext
   * or with an local account.
   *
   * @Route("/login", name="login")
   * @Template
   *
   * @param Request $request
   *
   * @return array|RedirectResponse
   */
  public function login(Request $request)
  {
    if ($this->isGranted('ROLE_USER')) {
      return $this->redirectToRoute('app_default_index');
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
      $trans = $this->get('translator');
      if ($authError instanceof BadCredentialsException) {
        // Bad credentials given
        $this->addFlash('authError', $trans->trans('login.bad-credentials'));
      } else if ($authError instanceof DisabledException) {
        // Account is disabled
        $this->addFlash('authError', $trans->trans('login.account-disabled'));
      } else {
        // General error occurred
        $this->addFlash('authError', $trans->trans('login.general-error'));
      }
    }

    return [
        'form' => $form->createView(),
    ];
  }

  /**
   * This controller forward the user to the SURFconext login
   *
   * @Route("/login_surf", name="login_surf")
   *
   * @param OidcClient $oidc
   *
   * @return RedirectResponse
   *
   * @throws \App\Oidc\Exception\OidcConfigurationException
   * @throws \App\Oidc\Exception\OidcConfigurationResolveException
   */
  public function surfconext(OidcClient $oidc)
  {
    // Redirect to authorization @ surfconext
    return $oidc->generateAuthorizationRedirect();
  }
}
