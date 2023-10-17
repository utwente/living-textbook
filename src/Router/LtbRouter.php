<?php

namespace App\Router;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

class LtbRouter
{
  private RouterInterface $router;

  public function __construct(RouterInterface $router)
  {
    $this->router = $router;
  }

  public function generate($name, $parameters = [], $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH)
  {
    return $this->router->generate($name, $parameters, $referenceType);
  }

  /** Generates an absolute url for a page, encapsulated by the browser page. */
  public function generateBrowserUrl(string $name, array $parameters = []): string
  {
    return $this->generateBrowserUrlForPath($this->router->generate($name, $parameters));
  }

  /** Generates an absolute url for a path, encapsulated by the browser page. */
  public function generateBrowserUrlForPath(string $path): string
  {
    return $this->router->generate('_home_simple', [
        'pageUrl' => ltrim($path, '/'),
    ], RouterInterface::ABSOLUTE_URL);
  }

  public function getRouteCollection()
  {
    return $this->router->getRouteCollection();
  }

  public function match($pathinfo)
  {
    return $this->router->match($pathinfo);
  }

  public function setContext(RequestContext $context)
  {
    $this->router->setContext($context);
  }

  public function getContext()
  {
    return $this->router->getContext();
  }
}
