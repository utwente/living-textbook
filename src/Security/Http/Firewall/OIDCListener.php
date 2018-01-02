<?php

namespace App\Security\Http\Firewall;

use App\Security\Core\Authentication\Token\OIDCToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;

class OIDCListener extends AbstractAuthenticationListener
{

  /**
   * Performs authentication.
   *
   * @param Request $request
   * @return TokenInterface|Response|null The authenticated token, null if full authentication is not possible, or a Response
   *
   * @throws AuthenticationException if the authentication fails
   */
  protected function attemptAuthentication(Request $request)
  {
    $token = new OIDCToken();

    return $this->authenticationManager->authenticate($token);
  }
}
