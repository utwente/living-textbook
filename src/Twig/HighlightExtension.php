<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class HighlightExtension.
 *
 * Register a filter in Twig to highlight parts of text, useful for search
 */
class HighlightExtension extends AbstractExtension
{
  /**
   * Register filters.
   *
   * @return array|TwigFilter[]
   */
  public function getFilters()
  {
    return [
        new TwigFilter('highlight', $this->hilightFilter(...), ['is_safe' => ['html']]),
    ];
  }

  /**
   * Filter implementation.
   *
   * @param $text
   * @param $search
   *
   * @return string
   */
  public function hilightFilter($text, $search)
  {
    return preg_replace(sprintf('/(%s)/i', preg_quote((string)$search)), '<b>$1</b>', (string)$text);
  }
}
