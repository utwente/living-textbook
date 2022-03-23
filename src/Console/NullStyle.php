<?php

namespace App\Console;

use RuntimeException;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * Class NullStyle
 * Can be used to disable output when the OutputStyle is not provided.
 */
class NullStyle extends OutputStyle
{
  public function title($message)
  {
  }

  public function section($message)
  {
  }

  public function listing(array $elements)
  {
  }

  public function text($message)
  {
  }

  public function success($message)
  {
  }

  public function error($message)
  {
  }

  public function warning($message)
  {
  }

  public function note($message)
  {
  }

  public function caution($message)
  {
  }

  public function table(array $headers, array $rows)
  {
  }

  public function ask($question, $default = null, $validator = null)
  {
    throw new RuntimeException(sprintf('"ask" not supported with %s', self::class));
  }

  public function askHidden($question, $validator = null)
  {
    throw new RuntimeException(sprintf('"askHidden" not supported with %s', self::class));
  }

  public function confirm($question, $default = true)
  {
    throw new RuntimeException(sprintf('"confirm" not supported with %s', self::class));
  }

  public function choice($question, array $choices, $default = null)
  {
    throw new RuntimeException(sprintf('"choice" not supported with %s', self::class));
  }

  public function progressStart($max = 0)
  {
  }

  public function progressAdvance($step = 1)
  {
  }

  public function progressFinish()
  {
  }
}
