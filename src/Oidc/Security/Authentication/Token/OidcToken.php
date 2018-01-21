<?php

namespace App\Oidc\Security\Authentication\Token;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OidcToken extends AbstractToken
{

  /**
   * @var array
   */
  private $userData;

  /**
   * OidcToken constructor.
   *
   * @param array $roles
   */
  public function __construct(array $roles = array())
  {
    parent::__construct($roles);

    $this->setAuthenticated(count($roles) > 0);
  }

  /**
   * Returns the user credentials.
   *
   * @return mixed The user credentials
   */
  public function getCredentials()
  {
    // No credentials available here
    return NULL;
  }

  /**
   * @param $userData
   *
   * @return OidcToken
   */
  public function setUserData(array $userData)
  {
    $this->userData = $userData;

    return $this;
  }

  public function getDisplayName()
  {
    return $this->getUserData('preferred_username');
  }

  public function getFullName()
  {
    return $this->getUserData('name');
  }

  public function getGivenName()
  {
    return $this->getUserData('given_name');
  }

  public function getFamilyName()
  {
    return $this->getUserData('family_name');
  }

  /**
   * @return string
   */
  public function getUsername()
  {
    if ($this->getUser() !== NULL) {
      return parent::getUsername();
    }

    return $this->getUserData('email');
  }

  /**
   * @param string $key
   *
   * @return string
   */
  private function getUserData(string $key): string
  {
    if (array_key_exists($key, $this->userData)) {
      return $this->userData[$key];
    }

    return '';
  }
}
