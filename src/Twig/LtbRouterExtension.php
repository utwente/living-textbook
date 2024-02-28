<?php

namespace App\Twig;

use App\Router\LtbRouter;
use Override;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LtbRouterExtension extends AbstractExtension
{
  private LtbRouter $router;

  public function __construct(LtbRouter $router)
  {
    $this->router = $router;
  }

  #[Override]
  public function getFunctions()
  {
    return [
      new TwigFunction('browserPath', $this->browserPath(...)),
    ];
  }

  public function browserPath($name, $parameters = [])
  {
    return $this->router->generateBrowserUrl($name, $parameters);
  }
}
