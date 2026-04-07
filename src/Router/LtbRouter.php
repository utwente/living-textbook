<?php

namespace App\Router;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

use function ltrim;

readonly class LtbRouter
{
  public function __construct(private RouterInterface $router)
  {
  }

  /** @phpstan-ignore missingType.iterableValue */
  public function generate(string $name, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string
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
    ], UrlGeneratorInterface::ABSOLUTE_URL);
  }

  public function getRouteCollection(): RouteCollection
  {
    return $this->router->getRouteCollection();
  }

  /** @phpstan-ignore missingType.iterableValue */
  public function match(string $pathinfo): array
  {
    return $this->router->match($pathinfo);
  }

  public function setContext(RequestContext $context): void
  {
    $this->router->setContext($context);
  }

  public function getContext(): RequestContext
  {
    return $this->router->getContext();
  }
}
