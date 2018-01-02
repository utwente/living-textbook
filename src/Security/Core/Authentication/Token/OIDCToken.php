<?php

namespace App\Security\Core\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OIDCToken extends AbstractToken
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
