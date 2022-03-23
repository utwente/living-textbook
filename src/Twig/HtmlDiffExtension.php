<?php

namespace App\Twig;

use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class HtmlDiffExtension extends AbstractExtension
{
  /**
   * Register functions.
   *
   * @return array|TwigFilter[]
   */
  public function getFunctions()
  {
    return [
        new TwigFunction('htmldiff', [$this, 'htmlDiff'], ['is_safe' => ['html']]),
    ];
  }

  public function htmlDiff(?string $a, ?string $b): string
  {
    $config = (new HtmlDiffConfig())
        ->setPurifierEnabled(false);

    return (HtmlDiff::create($a ?? '', $b ?? '', $config))->build();
  }
}
