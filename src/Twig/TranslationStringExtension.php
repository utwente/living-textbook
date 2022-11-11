<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslationStringExtension extends AbstractExtension
{
  /**
   * Register filters.
   *
   * @return array|TwigFilter[]
   */
  public function getFilters()
  {
    return [
        new TwigFilter('trString', $this->trString(...), ['is_safe' => ['html']]),
    ];
  }

  /**
   * Filter implementation.
   *
   * @param $text
   *
   * @return string
   */
  public function trString($text)
  {
    return strtolower(preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], '\1-\2', (string)$text));
  }
}
