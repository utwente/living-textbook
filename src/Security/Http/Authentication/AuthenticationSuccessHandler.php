<?php

namespace App\Security\Http\Authentication;

use App\Router\LtbRouter;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class AuthenticationSuccessHandler.
 *
 * Verifies the original target url, and insert the map if required
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{
  private LtbRouter $router;

  public function __construct(LtbRouter $router, HttpUtils $httpUtils, array $options = [])
  {
    parent::__construct($httpUtils, $options);

    $this->router = $router;
  }

  /**
   * This is called when an interactive authentication attempt succeeds. This
   * is called by authentication listeners inheriting from
   * AbstractAuthenticationListener.
   *
   * @return Response never null
   */
  public function onAuthenticationSuccess(Request $request, TokenInterface $token)
  {
    // Original target url, as determined by Symfony
    $targetUrl = $this->determineTargetUrl($request);
    /** @phan-suppress-next-line PhanTypePossiblyInvalidDimOffset */
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
