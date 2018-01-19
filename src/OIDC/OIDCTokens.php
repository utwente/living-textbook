<?php

namespace App\OIDC;
use App\Exception\OIDCException;

/**
 * Class OIDCTokens
 * Contains the access and id tokens retrieved from OpenID authentication
 *
 * @author BobV
 */
class OIDCTokens
{

  /**
   * @var string
   */
  private $accessToken;

  /**
   * @var string
   */
  private $idToken;

  /**
   * OIDCTokens constructor.
   *
   * @param \stdClass $tokens
   *
   * @throws OIDCException
   */
  public function __construct(\stdClass $tokens)
  {
    if (!isset($tokens->id_token) || !isset($tokens->access_token)) {
      throw new OIDCException("Invalid token object.");
    }
    $this->accessToken = $tokens->access_token;
    $this->idToken     = $tokens->id_token;
  }

  /**
   * @return string
   */
  public function getAccessToken(): string
  {
    return $this->accessToken;
  }

  /**
   * @return string
   */
  public function getIdToken(): string
  {
    return $this->idToken;
  }
}
