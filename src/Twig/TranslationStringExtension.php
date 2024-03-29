<?php

namespace App\Twig;

use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TranslationStringExtension extends AbstractExtension
{
  /**
   * Register filters.
   *
   * @return array|TwigFilter[]
   */
  #[Override]
  public function getFilters()
  {
    return [
      new TwigFilter('trString', $this->trString(...), ['is_safe' => ['html']]),
    ];
  }

  /**
   * Filter implementation.
   *
   * @return string
   */
  public function trString($text)
  {
    return strtolower((string)preg_replace(['/([A-Z]+)([A-Z][a-z])/', '/([a-z\d])([A-Z])/'], '\1-\2', (string)$text));
  }
}
