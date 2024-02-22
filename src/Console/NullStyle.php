<?php

namespace App\Console;

use Override;
use RuntimeException;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * Class NullStyle
 * Can be used to disable output when the OutputStyle is not provided.
 */
class NullStyle extends OutputStyle
{
  #[Override]
  public function title($message)
  {
  }

  #[Override]
  public function section($message)
  {
  }

  #[Override]
  public function listing(array $elements)
  {
  }

  #[Override]
  public function text($message)
  {
  }

  #[Override]
  public function success($message)
  {
  }

  #[Override]
  public function error($message)
  {
  }

  #[Override]
  public function warning($message)
  {
  }

  #[Override]
  public function note($message)
  {
  }

  #[Override]
  public function caution($message)
  {
  }

  #[Override]
  public function table(array $headers, array $rows)
  {
  }

  #[Override]
  public function ask($question, $default = null, $validator = null)
  {
    throw new RuntimeException(sprintf('"ask" not supported with %s', self::class));
  }

  #[Override]
  public function askHidden($question, $validator = null)
  {
    throw new RuntimeException(sprintf('"askHidden" not supported with %s', self::class));
  }

  #[Override]
  public function confirm($question, $default = true)
  {
    throw new RuntimeException(sprintf('"confirm" not supported with %s', self::class));
  }

  #[Override]
  public function choice($question, array $choices, $default = null)
  {
    throw new RuntimeException(sprintf('"choice" not supported with %s', self::class));
  }

  #[Override]
  public function progressStart($max = 0)
  {
  }

  #[Override]
  public function progressAdvance($step = 1)
  {
  }

  #[Override]
  public function progressFinish()
  {
  }
}
