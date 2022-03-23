<?php

namespace App\Analytics\Exception;

use RuntimeException;
use Throwable;

class VisualisationException extends RuntimeException
{
  public function __construct(Throwable $previous = null)
  {
    parent::__construct('Something unexpected happened during visualisation generation', 0, $previous);
  }
}
