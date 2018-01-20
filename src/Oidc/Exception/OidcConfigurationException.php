<?php

namespace App\Oidc\Exception;

class OidcConfigurationException extends OidcException
{
  public function __construct(string $key)
  {
    parent::__construct(sprintf('Configuration key "%s" does not exist.', $key));
  }
}
