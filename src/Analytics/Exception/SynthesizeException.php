<?php

namespace App\Analytics\Exception;

use RuntimeException;
use Throwable;

class SynthesizeException extends RuntimeException
{
  public function __construct(Throwable $previous = NULL)
  {
    parent::__construct('Something unexpected happened during visualisation generation', 0, $previous);
  }
}
