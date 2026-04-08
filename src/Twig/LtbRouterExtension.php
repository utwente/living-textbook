<?php

namespace App\Twig;

use App\Router\LtbRouter;
use Twig\Attribute\AsTwigFunction;

class LtbRouterExtension
{
  public function __construct(private readonly LtbRouter $router)
  {
  }

  #[AsTwigFunction(name: 'browserPath')]
  public function browserPath($name, $parameters = []): string
  {
    return $this->router->generateBrowserUrl($name, $parameters);
  }
}
