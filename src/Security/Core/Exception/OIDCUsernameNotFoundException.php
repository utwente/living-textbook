<?php

namespace App\Security\Core\Exception;

use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OIDCUsernameNotFoundException extends UsernameNotFoundException
{
  public function __construct(UsernameNotFoundException $previous = NULL)
  {
    parent::__construct('', 0, $previous);
  }
}
