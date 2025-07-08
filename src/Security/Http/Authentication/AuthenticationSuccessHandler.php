<?php

namespace App\Security\Http\Authentication;

use App\Router\LtbRouter;
use Exception;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

use function parse_url;

/**
 * Verifies the original target url, and insert the map if required.
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
  public function __construct(
    private readonly LtbRouter $router,
    HttpUtils $httpUtils,
    array $options = [])
  {
    parent::__construct($httpUtils, $options);
  }

  /**
   * This is called when an interactive authentication attempt succeeds. This
   * is called by authentication listeners inheriting from
   * AbstractAuthenticationListener.
   */
  #[Override]
  public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
  {
    // Original target url, as determined by Symfony
    $targetUrl  = $this->determineTargetUrl($request);
    $targetPath = parse_url($targetUrl)['path'];

    // Determine whether to wrap this url or not
    try {
      $matchedRoute = $this->router->match($targetPath);
    } catch (Exception) {
      // On exception, use the original forward
      return $this->httpUtils->createRedirectResponse($request, $targetUrl);
    }

    // Retrieve route information
    $routeInfo = $this->router->getRouteCollection()->get($matchedRoute['_route']);

    // Verify whether this route must not be wrapped after login
    if ($routeInfo->getOption('no_login_wrap') === true) {
      return $this->httpUtils->createRedirectResponse($request, $targetUrl);
    }

    // Wrap path
    return $this->httpUtils->createRedirectResponse($request, $this->router->generateBrowserUrlForPath($targetPath));
  }
}
