<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class HighlightExtension
 *
 * Register a filter in Twig to highlight parts of text, useful for search
 */
class HighlightExtension extends AbstractExtension
{

  /**
   * Register filters
   *
   * @return array|\Twig_Filter[]
   */
  public function getFilters()
  {
    return array(
        new TwigFilter('highlight', array($this, 'hilightFilter'), array('is_safe' => array('html'))),
    );
  }

  /**
   * Filter implementation
   *
   * @param $text
   * @param $search
   *
   * @return string
   */
  public function hilightFilter($text, $search)
  {
    return preg_replace(sprintf('/(%s)/i', preg_quote($search)), '<b>$1</b>', $text);
  }
}
