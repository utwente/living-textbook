<?php

namespace App\Analytics\Exception;

use RuntimeException;
use Throwable;

class VisualisationException extends RuntimeException
{
  public function __construct(Throwable $previous = NULL)
  {
    parent::__construct('Something unexpected failed during visualisation generation', 0, $previous);
  }
}
