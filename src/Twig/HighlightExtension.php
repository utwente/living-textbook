<?php

namespace App\Twig;

use Twig\Attribute\AsTwigFilter;

use function preg_quote;
use function preg_replace;
use function sprintf;

/** Register a filter in Twig to highlight parts of text, useful for search. */
class HighlightExtension
{
  /** Filter implementation. */
  #[AsTwigFilter(name: 'highlight', isSafe: ['html'])]
  public function hilightFilter($text, $search): string
  {
    return preg_replace(sprintf('/(%s)/i', preg_quote((string)$search)), '<b>$1</b>', (string)$text);
  }
}
