<?php

namespace App\OIDC;

use App\Exception\OIDCConfigurationException;
use App\Exception\OIDCConfigurationResolveException;
use App\Exception\OIDCException;
use App\Security\Core\Exception\OIDCAuthenticationException;
use phpseclib\Crypt\RSA;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class OIDCClient
 * This class implements the OIDC protocol.
 *
 * @author BobV
 */
class OIDCClient
{

  const OIDC_SESSION_STATE = 'oidc.session.state';

  /**
   * @var SessionInterface
   */
  protected $session;

  /**
   * @var RouterInterface
   */
  protected $router;

  /**
   * @var OIDCUrlFetcher
   */
  protected $urlFetcher;

  /**
   * OIDC well-known location
   *
   * @var string
   */
  protected $wellKnownUrl;

  /**
   * OIDC configuration values
   *
   * @var array|null
   */
  protected $configuration;

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
   * @param string           $wellKnownUrl
   * @param string           $clientId
   * @param string           $clientSecret
   */
  public function __construct(SessionInterface $session, RouterInterface $router, string $wellKnownUrl, string $clientId, string $clientSecret)
  {
    // Check for required phpseclib classes
    if (!class_exists('\phpseclib\Crypt\RSA')) {
      throw new \RuntimeException('Unable to find phpseclib Crypt/RSA.php.  Ensure phpseclib/phpseclib is installed.');
    }

    $this->session      = $session;
    $this->router       = $router;
    $this->wellKnownUrl = $wellKnownUrl;
    $this->clientId     = $clientId;
    $this->clientSecret = $clientSecret;

    $this->urlFetcher = new OIDCUrlFetcher();
  }

  /**
   * Authenticate the incoming request
   *
   * @param Request $request
   *
   * @return OIDCTokens|null
   * @throws OIDCException
   */
  public function authenticate(Request $request)
  {
    // Check whether the request has an error state
    if ($request->request->has('error')) {
      throw new OIDCAuthenticationException(sprintf("OIDC error: %s. Description: %s.",
          $request->request->get('error', ''), $request->request->get('error_description', '')));
    }

    // Check whether the request contains the required state and code keys
    $code  = $request->query->get('code');
    $state = $request->query->get('state');
    if ($code == NULL || $state == NULL) {
      return NULL;
    }

    // Do a session check
    if ($state != $request->getSession()->get(self::OIDC_SESSION_STATE)) {
      // Fail silently
      return NULL;
    }

    // Clear session after check
    $request->getSession()->remove(self::OIDC_SESSION_STATE);

    // Request the tokens
    $tokens = $this->requestTokens($code);

    // Check for id token
    if (!isset($tokens->id_token) || !isset($tokens->access_token)) {
      throw new OIDCAuthenticationException("Id token not found in the token response.");
    }

    // Retrieve the claims
    $claims = $this->decodeJWT($tokens->id_token, 1);

    // Verify the token
    if (!$this->verifyJWTSignature($tokens->id_token)) {
      throw new OIDCAuthenticationException ("Unable to verify signature");
    }

    // If this is a valid claim
    /** @noinspection PhpUndefinedFieldInspection */
    if ($this->verifyJWTClaims($claims, $tokens->access_token)) {
      return new OIDCTokens($tokens);
    } else {
      throw new OIDCAuthenticationException("Unable to verify JWT claims");
    }

  }

  /**
   * Create the redirect that should be followed in order to authorize
   *
   * @return RedirectResponse
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  public function generateAuthorizationRedirect(): RedirectResponse
  {
    $data = [
        'client_id'     => $this->clientId,
        'response_type' => 'code',
        'redirect_uri'  => $this->getRedirectUrl(),
        'scope'         => 'openid',
        'state'         => $this->generateState(),
    ];

    return new RedirectResponse($this->getAuthorizationEndpoint() . '?' . http_build_query($data));
  }

  /**
   * Retrieve the user information
   *
   * @param OIDCTokens $tokens
   *
   * @return mixed
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  public function retrieveUserInfo(OIDCTokens $tokens)
  {
    $headers = ["Authorization: Bearer {$tokens->getAccessToken()}"];

    return json_decode($this->urlFetcher->fetchURL($this->getUserinfoEndpoint(), NULL, $headers));
  }

  /**
   * @return mixed
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  protected function getAuthorizationEndpoint()
  {
    return $this->getConfigurationValue('authorization_endpoint');
  }

  /**
   * @return mixed
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  protected function getEndSessionEndpoint()
  {
    return $this->getConfigurationValue('end_session_endpoint');
  }

  /**
   * @return string
   */
  protected function getRedirectUrl()
  {
    return $this->router->generate('login_check', [], RouterInterface::ABSOLUTE_URL);
  }

  /**
   * @return mixed
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  protected function getTokenEndpoint()
  {
    return $this->getConfigurationValue('token_endpoint');
  }


  /**
   * @return mixed
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  protected function getUserinfoEndpoint()
  {
    return $this->getConfigurationValue('userinfo_endpoint');
  }

  /**
   * @param     $jwt     string encoded JWT
   * @param int $section the section we would like to decode
   *
   * @return object
   */
  private function decodeJWT($jwt, $section = 0)
  {

    $parts = explode(".", $jwt);

    return json_decode(self::base64url_decode($parts[$section]));
  }

  /**
   * Generate a state to identify the request
   *
   * @return string
   */
  private function generateState(): string
  {
    $value = $this->generateRandomString();
    $this->session->set(self::OIDC_SESSION_STATE, $value);

    return $value;
  }

  /**
   * Generate a secure random string for usage as state
   *
   * @return string
   */
  private function generateRandomString(): string
  {
    return md5(openssl_random_pseudo_bytes(25));
  }

  /**
   * @param $accessToken
   *
   * @return object
   */
  private function getAccessTokenHeader($accessToken)
  {
    return $this->decodeJWT($accessToken, 0);
  }

  /**
   * @param string $key
   *
   * @return mixed
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  private function getConfigurationValue(string $key)
  {
    // Resolve the configuration
    $this->resolveConfiguration();

    if (!array_key_exists($key, $this->configuration)) {
      throw new OIDCConfigurationException($key);
    }

    return $this->configuration[$key];
  }

  /**
   * @param $keys
   * @param $header
   *
   * @return mixed
   */
  private function getKeyForHeader($keys, $header)
  {
    foreach ($keys as $key) {
      if ($key->kty == 'RSA') {
        if (!isset($header->kid) || $key->kid == $header->kid) {
          return $key;
        }
      } else {
        if ($key->alg == $header->alg && $key->kid == $header->kid) {
          return $key;
        }
      }
    }
    if (isset($header->kid)) {
      throw new OIDCAuthenticationException('Unable to find a key for (algorithm, kid):' . $header->alg . ', ' . $header->kid . ')');
    } else {
      throw new OIDCAuthenticationException('Unable to find a key for RSA');
    }
  }

  /**
   * Retrieves the well-known configuration and saves it in the class
   *
   * @throws OIDCConfigurationResolveException
   */
  private function resolveConfiguration()
  {
    // Check whether the configuration is already available
    if ($this->configuration !== NULL) return;

    // Retrieve the configuration data
    if (($response = file_get_contents($this->wellKnownUrl)) === false) {
      throw new OIDCConfigurationResolveException(sprintf('Could not retrieve OIDC configuration from "%s".', $this->wellKnownUrl));
    }

    // Parse the configuration
    if (($config = json_decode($response, true)) === NULL) {
      throw new OIDCConfigurationResolveException(sprintf('Could not parse OIDC configuration. Response data: "%s"', $response));
    }

    // Set the configuration
    $this->configuration = $config;
  }

  /**
   * @param $code
   *
   * @return \stdClass
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  private function requestTokens($code)
  {
    $params = [
        'grant_type'    => 'authorization_code',
        'code'          => $code,
        'redirect_uri'  => $this->getRedirectUrl(),
        'client_id'     => $this->clientId,
        'client_secret' => $this->clientSecret,
    ];

    // Use basic auth if offered
    $headers = [];
    if (in_array('client_secret_basic', $this->getConfigurationValue('token_endpoint_auth_methods_supported'))) {
      $headers = ['Authorization: Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret)];
      unset($params['client_secret']);
    }

    $jsonToken = json_decode($this->urlFetcher->fetchUrl($this->getTokenEndpoint(), $params, $headers));

    // Throw an error if the server returns one
    if (isset($jsonToken->error)) {
      if (isset($jsonToken->error_description)) {
        throw new OIDCAuthenticationException($jsonToken->error_description);
      }
      throw new OIDCAuthenticationException(sprintf('Got response: %s', $jsonToken->error));
    }

    return $jsonToken;
  }

  /**
   * @param      $claims
   * @param null $accessToken
   *
   * @return bool
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  private function verifyJWTClaims($claims, $accessToken = NULL)
  {
    if (isset($claims->at_hash) && isset($accessToken)) {
      $accessTokenHeader = $this->getAccessTokenHeader($accessToken);
      if (isset($accessTokenHeader->alg) && $accessTokenHeader->alg != 'none') {
        $bit = substr($accessTokenHeader->alg, 2, 3);
      } else {
        // TODO: Error case. throw exception???
        $bit = '256';
      }
      $len            = ((int)$bit) / 16;
      $expectedAtHash = self::urlEncode(substr(hash('sha' . $bit, $accessToken, true), 0, $len));
    }

    /** @noinspection PhpUndefinedVariableInspection */
    return (($claims->iss == $this->getConfigurationValue('issuer'))
        && (($claims->aud == $this->clientId) || (in_array($this->clientId, $claims->aud)))
        && (!isset($claims->exp) || $claims->exp >= time())
        && (!isset($claims->nbf) || $claims->nbf <= time())
        && (!isset($claims->at_hash) || $claims->at_hash == $expectedAtHash)
    );
  }

  /**
   * @param string $jwt encoded JWT
   *
   * @return bool
   * @throws OIDCConfigurationException
   * @throws OIDCConfigurationResolveException
   */
  private function verifyJWTSignature($jwt)
  {
    // Check JWT information
    if (!$this->getConfigurationValue('jwks_uri')) {
      throw new OIDCAuthenticationException("Unable to verify signature due to no jwks_uri being defined");
    }

    $parts     = explode(".", $jwt);
    $signature = self::base64url_decode(array_pop($parts));
    $header    = json_decode(self::base64url_decode($parts[0]));
    $payload   = implode(".", $parts);
    $jwks      = json_decode($this->urlFetcher->fetchURL($this->getConfigurationValue('jwks_uri')));
    if ($jwks === NULL) {
      throw new OIDCAuthenticationException('Error decoding JSON from jwks_uri');
    }

    // Check for supported signature types
    if (!in_array($header->alg, ['RS256', 'RS384', 'RS512'])) {
      throw new OIDCAuthenticationException('No support for signature type: ' . $header->alg);
    }

    $hashType = 'sha' . substr($header->alg, 2);

    return $this->verifyRSAJWTSignature($hashType, $this->getKeyForHeader($jwks->keys, $header), $payload, $signature);
  }

  /**
   * @param string $hashtype
   * @param object $key
   * @param        $payload
   * @param        $signature
   *
   * @return bool
   */
  private function verifyRSAJWTSignature($hashtype, $key, $payload, $signature)
  {
    if (!(property_exists($key, 'n') and property_exists($key, 'e'))) {
      throw new OIDCAuthenticationException('Malformed key object');
    }

    /**
     * We already have base64url-encoded data, so re-encode it as
     * regular base64 and use the XML key format for simplicity.
     */
    $public_key_xml = "<RSAKeyValue>\r\n" .
        "  <Modulus>" . self::b64url2b64($key->n) . "</Modulus>\r\n" .
        "  <Exponent>" . self::b64url2b64($key->e) . "</Exponent>\r\n" .
        "</RSAKeyValue>";

    $rsa = new RSA();
    $rsa->setHash($hashtype);
    $rsa->loadKey($public_key_xml, RSA::PUBLIC_FORMAT_XML);
    $rsa->signatureMode = RSA::SIGNATURE_PKCS1;

    return $rsa->verify($payload, $signature);
  }

  /**
   * A wrapper around base64_decode which decodes Base64URL-encoded data,
   * which is not the same alphabet as base64.
   *
   * @param $base64url
   *
   * @return bool|string
   */
  private static function base64url_decode($base64url)
  {
    return base64_decode(self::b64url2b64($base64url));
  }

  /**
   * Per RFC4648, "base64 encoding with URL-safe and filename-safe
   * alphabet".  This just replaces characters 62 and 63.  None of the
   * reference implementations seem to restore the padding if necessary,
   * but we'll do it anyway.
   *
   * @param $base64url
   *
   * @return string
   */
  private static function b64url2b64($base64url)
  {
    // "Shouldn't" be necessary, but why not
    $padding = strlen($base64url) % 4;
    if ($padding > 0) {
      $base64url .= str_repeat("=", 4 - $padding);
    }

    return strtr($base64url, '-_', '+/');
  }


  /**
   * @param string $str
   *
   * @return string
   */
  private static function urlEncode($str)
  {
    $enc = base64_encode($str);
    $enc = rtrim($enc, "=");
    $enc = strtr($enc, "+/", "-_");

    return $enc;
  }
}
