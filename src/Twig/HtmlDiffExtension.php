<?php

namespace App\Twig;

use Caxy\HtmlDiff\HtmlDiff;
use Caxy\HtmlDiff\HtmlDiffConfig;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class HtmlDiffExtension extends AbstractExtension
{
  /** @return TwigFunction[] */
  #[Override]
  public function getFunctions(): array
  {
    return [
      new TwigFunction('htmldiff', $this->htmlDiff(...), ['is_safe' => ['html']]),
    ];
  }

  public function htmlDiff(?string $a, ?string $b): string
  {
    $config = (new HtmlDiffConfig())
      ->setPurifierEnabled(false);

    return HtmlDiff::create($a ?? '', $b ?? '', $config)->build();
  }
}
