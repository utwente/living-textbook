<?php

namespace App\Twig;

use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use function preg_replace;
use function strtolower;

class TranslationStringExtension extends AbstractExtension
{
  /** @return TwigFilter[] */
  #[Override]
  public function getFilters(): array
  {
    return [
      new TwigFilter('trString', $this->trString(...), ['is_safe' => ['html']]),
    ];
  }

  /** Filter implementation. */
  public function trString($text): string
  {
    return strtolower((string)preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], '\1-\2', (string)$text));
  }
}
