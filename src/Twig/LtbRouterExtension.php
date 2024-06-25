<?php

namespace App\Twig;

use App\Router\LtbRouter;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LtbRouterExtension extends AbstractExtension
{
  public function __construct(private readonly LtbRouter $router)
  {
  }

  #[Override]
  public function getFunctions(): array
  {
    return [
      new TwigFunction('browserPath', $this->browserPath(...)),
    ];
  }

  public function browserPath($name, $parameters = []): string
  {
    return $this->router->generateBrowserUrl($name, $parameters);
  }
}
