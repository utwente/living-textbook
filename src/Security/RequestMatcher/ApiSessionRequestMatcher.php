<?php

namespace App\Security\RequestMatcher;

use App\Api\Security\ApiAuthenticator;
use Override;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestMatcherInterface;

class ApiSessionRequestMatcher implements RequestMatcherInterface
{
  final public const string SESSION_API_TOKEN = 'session';

  #[Override]
  public function matches(Request $request): bool
  {
    // If a special header value is used, the firewall is bypassed to allow session authentication
    return preg_match('@^/api/(?!doc(\.json)?$)@', $request->getPathInfo()) > 0
        && $request->headers->get(ApiAuthenticator::API_TOKEN_HEADER) !== self::SESSION_API_TOKEN;
  }
}
