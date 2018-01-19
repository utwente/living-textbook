<?php

namespace App\Exception;

class OIDCConfigurationException extends OIDCException
{
  public function __construct(string $key)
  {
    parent::__construct(sprintf('Configuration key "%s" does not exist.', $key));
  }
}
