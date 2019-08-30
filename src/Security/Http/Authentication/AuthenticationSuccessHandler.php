<?php

namespace App\Security\Http\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

/**
 * Class AuthenticationSuccessHandler
 *
 * Verifies the original target url, and insert the map if required
 */
class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{

  /** @var RouterInterface */
  private $router;

  public function __construct(RouterInterface $router, HttpUtils $httpUtils, array $options = [])
  {
    parent::__construct($httpUtils, $options);

    $this->router = $router;
  }

  /**
   * This is called when an interactive authentication attempt succeeds. This
   * is called by authentication listeners inheriting from
   * AbstractAuthenticationListener.
   *
   * @param Request        $request
   * @param TokenInterface $token
   *
   * @return Response never null
   */
  public function onAuthenticationSuccess(Request $request, TokenInterface $token)
  {
    // Original target url, as determined by Symfony
    $targetUrl  = $this->determineTargetUrl($request);
    $targetPath = parse_url($targetUrl)['path'];

    // Determine whether to wrap this url or not
    try {
      $matchedRoute = $this->router->match($targetPath);
    } catch (\Exception $e) {
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
    return $this->httpUtils->createRedirectResponse($request,
        $this->router->generate('_home_simple', ['pageUrl' => ltrim($targetPath, '/')], UrlGeneratorInterface::ABSOLUTE_URL));
  }
}
