<?php

namespace App\Twig;

use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use function preg_quote;
use function preg_replace;
use function sprintf;

/**
 * Register a filter in Twig to highlight parts of text, useful for search.
 */
class HighlightExtension extends AbstractExtension
{
  /** @return TwigFilter[] */
  #[Override]
  public function getFilters(): array
  {
    return [
      new TwigFilter('highlight', $this->hilightFilter(...), ['is_safe' => ['html']]),
    ];
  }

  /** Filter implementation. */
  public function hilightFilter($text, $search): string
  {
    return preg_replace(sprintf('/(%s)/i', preg_quote((string)$search)), '<b>$1</b>', (string)$text);
  }
}
