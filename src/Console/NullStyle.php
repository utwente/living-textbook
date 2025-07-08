<?php

namespace App\Console;

use Override;
use RuntimeException;
use Symfony\Component\Console\Style\OutputStyle;

use function sprintf;

/**
 * Can be used to disable output when the OutputStyle is not provided.
 */
class NullStyle extends OutputStyle
{
  #[Override]
  public function title(string $message): void
  {
  }

  #[Override]
  public function section(string $message): void
  {
  }

  #[Override]
  public function listing(array $elements): void
  {
  }

  #[Override]
  public function text($message): void
  {
  }

  #[Override]
  public function success($message): void
  {
  }

  #[Override]
  public function error($message): void
  {
  }

  #[Override]
  public function warning($message): void
  {
  }

  #[Override]
  public function note($message): void
  {
  }

  #[Override]
  public function caution($message): void
  {
  }

  #[Override]
  public function table(array $headers, array $rows): void
  {
  }

  #[Override]
  public function ask(string $question, ?string $default = null, ?callable $validator = null): never
  {
    throw new RuntimeException(sprintf('"ask" not supported with %s', self::class));
  }

  #[Override]
  public function askHidden(string $question, ?callable $validator = null): never
  {
    throw new RuntimeException(sprintf('"askHidden" not supported with %s', self::class));
  }

  #[Override]
  public function confirm(string $question, bool $default = true): never
  {
    throw new RuntimeException(sprintf('"confirm" not supported with %s', self::class));
  }

  #[Override]
  public function choice(string $question, array $choices, $default = null): never
  {
    throw new RuntimeException(sprintf('"choice" not supported with %s', self::class));
  }

  #[Override]
  public function progressStart(int $max = 0): void
  {
  }

  #[Override]
  public function progressAdvance(int $step = 1): void
  {
  }

  #[Override]
  public function progressFinish(): void
  {
  }
}
