<?php

namespace App\Oidc\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OidcToken extends AbstractToken
{
  /**
   * Returns the user credentials.
   *
   * @return mixed The user credentials
   */
  public function getCredentials()
  {
    // TODO: Implement getCredentials() method.
  }
}
