<?php

namespace App\Twig;

use App\Router\LtbRouter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class LtbRouterExtension extends AbstractExtension
{
  /** @var LtbRouter */
  private $router;

  public function __construct(LtbRouter $router)
  {
    $this->router = $router;
  }

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
