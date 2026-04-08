<?php

namespace App\Twig;

use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;
use Twig\Attribute\AsTwigFunction;

class HtmlDiffExtension
{
  #[AsTwigFunction(name: 'htmldiff', isSafe: ['html'])]
  public function htmlDiff(?string $a, ?string $b): string
  {
    $config = new HtmlDiffConfig()
      ->setPurifierEnabled(false);

    return HtmlDiff::create($a ?? '', $b ?? '', $config)->build();
  }
}
