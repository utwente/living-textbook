<?php

namespace App\Security;

use App\Entity\User;
use App\Oidc\Security\Authentication\Token\OidcToken;
use App\Oidc\Security\UserProvider\OidcUserProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements OidcUserProviderInterface
{

  /**
   * @var EntityManagerInterface
   */
  protected $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  /**
   * Call this method to create a new user from the data available in the token,
   * but only if the user does not exists yet.
   * If it does exist, return that user.
   *
   * @param OidcToken $token
   *
   * @return UserInterface
   */
  public function loadUserByToken(OidcToken $token)
  {
    // Determine whether this user already exists
    try {
      $user = $this->loadUserByUsername($token->getUsername());
    } catch (UsernameNotFoundException $e){
      // Create a new user
      $user = User::createFromToken($token);

      // Save the user
      $this->em->persist($user);
    }

    // Update last used
    $user->setLastUsed(new \DateTime());
    $this->em->flush();

    return $user;
  }

  /**
   * Loads the user for the given username.
   *
   * This method must throw UsernameNotFoundException if the user is not
   * found.
   *
   * @param string $username The username
   *
   * @return UserInterface
   *
   * @throws UsernameNotFoundException if the user is not found
   */
  public function loadUserByUsername($username)
  {
    $user = $this->em->getRepository('App:User')
        ->findOneBy(['username' => $username]);

    if (!$user) {
      throw new UsernameNotFoundException();
    }

    return $user;
  }

  /**
   * Refreshes the user.
   *
   * It is up to the implementation to decide if the user data should be
   * totally reloaded (e.g. from the database), or if the UserInterface
   * object can just be merged into some internal array of users / identity
   * map.
   *
   * @return UserInterface
   *
   * @throws UnsupportedUserException if the user is not supported
   */
  public function refreshUser(UserInterface $user)
  {
    return $this->loadUserByUsername($user->getUsername());
  }

  /**
   * Whether this provider supports the given user class.
   *
   * @param string $class
   *
   * @return bool
   */
  public function supportsClass($class)
  {
    return $class instanceof User;
  }
}
