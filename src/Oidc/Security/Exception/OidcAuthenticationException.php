<?php

namespace App\Oidc\Security\Exception;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OidcAuthenticationException extends AuthenticationException
{
  const TOKEN_UNSUPPORTED = 'Token unsupported';

  public function __construct(string $message = "", TokenInterface $token = NULL, \Throwable $previous = NULL)
  {
    parent::__construct($message, 0, $previous);
    $this->setToken($token);
  }
}
