<?php

namespace App\Security;

use App\Security\Core\Authentication\Provider\OIDCProvider;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

class OIDCClient
{

  /**
   * @var SessionInterface
   */
  protected $session;

  /**
   * @var RouterInterface
   */
  protected $router;

  /**
   * OIDC endpoint for retrieving user information
   *
   * @var string
   */
  protected $userinfoEndpoint;

  /**
   * OIDC endpoint for authorization
   *
   * @var string
   */
  protected $authorizationEndpoint;

  /**
   * OIDC endpoint for retrieving the token
   *
   * @var string
   */
  protected $tokenEndpoint;

  /**
   * OIDC Client ID
   *
   * @var string
   */
  private $clientId;

  /**
   * OIDC Client secret
   *
   * @var string
   */
  private $clientSecret;

  /**
   * OIDCConfiguration constructor.
   *
   * @param SessionInterface $session
   * @param RouterInterface  $router
   * @param string           $userinfoEndpoint
   * @param string           $authorizationEndpoint
   * @param string           $tokenEndpoint
   * @param string           $clientId
   * @param string           $clientSecret
   */
  public function __construct(SessionInterface $session, RouterInterface $router, string $userinfoEndpoint, string $authorizationEndpoint, string $tokenEndpoint, string $clientId, string $clientSecret)
  {
    $this->session               = $session;
    $this->router                = $router;
    $this->userinfoEndpoint      = $userinfoEndpoint;
    $this->authorizationEndpoint = $authorizationEndpoint;
    $this->tokenEndpoint         = $tokenEndpoint;
    $this->clientId              = $clientId;
    $this->clientSecret          = $clientSecret;
  }

  /**
   * Create the URL that should be followed in order to authorize
   *
   * @return RedirectResponse
   */
  public function generateAuthorizationRedirect(): RedirectResponse
  {
    $data = [
        'client_id'     => $this->clientId,
        'response_type' => 'code',
        'redirect_uri'  => $this->router->generate('login_check', [], RouterInterface::ABSOLUTE_URL),
        'scope'         => 'openid',
        'state'         => $this->generateState(),
        'nonce'         => $this->generateNonce(),
    ];

    return new RedirectResponse($this->authorizationEndpoint . '?' . http_build_query($data));
  }

  /**
   * Generate a nonce for authentication
   *
   * @return string
   */
  private function generateNonce(): string
  {
    $value = $this->generateRandomString();
    $this->session->set(OIDCProvider::OIDC_SESSION_NONCE, $value);

    return $value;
  }

  /**
   * Generate a state to identify the request
   *
   * @return string
   */
  private function generateState(): string
  {
    $value = $this->generateRandomString();
    $this->session->set(OIDCProvider::OIDC_SESSION_STATE, $value);

    return $value;
  }


  private function generateRandomString(): string
  {
    return md5(openssl_random_pseudo_bytes(25));
  }

}
