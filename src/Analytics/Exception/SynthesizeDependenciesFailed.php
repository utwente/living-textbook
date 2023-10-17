<?php

namespace App\Analytics\Exception;

use Exception;
use Throwable;

class SynthesizeDependenciesFailed extends Exception
{
  public function __construct(string $dependency, ?Throwable $previous = null)
  {
    parent::__construct(sprintf('Dependency %s failed to build', $dependency), 0, $previous);
  }
}
