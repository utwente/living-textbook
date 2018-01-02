<?php

namespace App\Security\Core\Authentication\Provider;

use App\Security\Core\Authentication\Token\OIDCToken;
use App\Security\Core\Exception\OIDCAuthenticationException;
use App\Security\Core\Exception\OIDCUsernameNotFoundException;
use App\Security\Core\User\OIDCUserProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class OIDCProvider implements AuthenticationProviderInterface
{

  const OIDC_SESSION_NONCE = 'oidc.session.nonce';
  const OIDC_SESSION_STATE = 'oidc.session.state';

  /**
   * @var OIDCUserProvider
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

  public function __construct(OIDCUserProvider $userProvider, UserCheckerInterface $userChecker, TokenStorageInterface $tokenStorage, LoggerInterface $logger)
  {
    $this->userProvider = $userProvider;
    $this->userChecker  = $userChecker;
    $this->tokenStorage = $tokenStorage;
    $this->logger       = $logger;
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
