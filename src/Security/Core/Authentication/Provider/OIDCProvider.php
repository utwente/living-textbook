<?php

namespace App\Security\Core\Authentication\Provider;

use App\Exception\OIDCException;
use App\OIDC\OIDCClient;
use App\Security\Core\Authentication\Token\OIDCToken;
use App\Security\Core\Exception\OIDCAuthenticationException;
use App\Security\Core\Exception\OIDCUsernameNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class OIDCProvider implements AuthenticationProviderInterface
{

  /**
   * @var UserProviderInterface
   */
  private $userProvider;

  /**
   * @var UserCheckerInterface
   */
  private $userChecker;

  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  /**
   * @var LoggerInterface
   */
  private $logger;

  /**
   * @var OIDCClient
   */
  private $OIDCClient;

  /**
   * @var Request
   */
  private $request;

  public function __construct(UserProviderInterface $userProvider, UserCheckerInterface $userChecker, TokenStorageInterface $tokenStorage, LoggerInterface $logger, OIDCClient $OIDCClient, RequestStack $request)
  {
    $this->userProvider = $userProvider;
    $this->userChecker  = $userChecker;
    $this->tokenStorage = $tokenStorage;
    $this->logger       = $logger;
    $this->OIDCClient   = $OIDCClient;
    $this->request      = $request->getMasterRequest();
  }

  /**
   * Attempts to authenticate a TokenInterface object.
   *
   * @param TokenInterface $token The TokenInterface instance to authenticate
   *
   * @return TokenInterface An authenticated TokenInterface instance, never null
   *
   * @throws AuthenticationException if the authentication fails
   */
  public function authenticate(TokenInterface $token)
  {
    // Check whether the token is supported
    if (!$this->supports($token)) {
      $this->logger->debug("OIDC Provider: Unsupported token supplied", array('token' => get_class($token)));
      throw new OIDCAuthenticationException(OIDCAuthenticationException::TOKEN_UNSUPPORTED, $token);
    }

    // Check if the token is already authenticated
    if ($token->isAuthenticated()) {
      $this->logger->debug("OIDC Provider: Token already authenticated", array('username' => $token->getUsername()));

      return $token;
    }

    // Try to validate the request
    try {
      if (($authData = $this->OIDCClient->authenticate($this->request)) === false) {
        return NULL;
      }

      $userData = $this->OIDCClient->retrieveUserInfo($authData);
    } catch (OIDCException $e) {
      throw new OIDCAuthenticationException("Request validation failed", null, $e);
    }


    // Retrieve the user
    try {
      $user = $this->userProvider->loadUserByUsername($token);
    } catch (UsernameNotFoundException $e) {
      $this->logger->debug("OIDC Provider: User not found", array('username' => $token->getUsername()));
      throw new OIDCUsernameNotFoundException($e);
    }

    // Check user
    $this->userChecker->checkPreAuth($user);
    $this->userChecker->checkPostAuth($user);

    $token = new OIDCToken();
    $token->setAuthenticated(true);

    return $token;
  }

  /**
   * Checks whether this provider supports the given token.
   *
   * @param TokenInterface $token
   *
   * @return bool true if the implementation supports the Token, false otherwise
   */
  public function supports(TokenInterface $token)
  {
    return $token instanceof OIDCToken;
  }
}
