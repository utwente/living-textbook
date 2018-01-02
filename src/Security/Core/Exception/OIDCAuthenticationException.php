<?php

namespace App\Security\Core\Exception;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class OIDCAuthenticationException extends AuthenticationException
{
  const TOKEN_UNSUPPORTED = 'Token unsupported';

  public function __construct(string $message = "", TokenInterface $token = NULL)
  {
    parent::__construct($message);
    $this->setToken($token);
  }
}
