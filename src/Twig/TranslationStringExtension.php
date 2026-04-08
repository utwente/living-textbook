<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

use function preg_replace;
use function strtolower;

class TranslationStringExtension
{
  /** Filter implementation. */
  #[AsTwigFilter(name: 'trString', isSafe: ['html'])]
  public function trString($text): string
  {
    return strtolower((string)preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], '\1-\2', (string)$text));
  }
}
