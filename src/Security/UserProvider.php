<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Drenso\OidcBundle\Exception\OidcException;
use Drenso\OidcBundle\Security\Authentication\Token\OidcToken;
use Drenso\OidcBundle\Security\UserProvider\OidcUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider implements OidcUserProviderInterface
{
  /** @var EntityManagerInterface */
  protected $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  /**
   * Call this method to create a new user from the data available in the token,
   * but only if the user does not exist yet.
   * If it does exist, return that user.
   *
   * @throws OidcException
   *
   * @return User
   */
  public function loadUserByToken(OidcToken $token)
  {
    // Determine whether this user already exists
    try {
      $user = $this->loadUserByIdentifier($token->getUserIdentifier(), true);
      $user->update($token);
    } catch (UserNotFoundException $e) {
      // Create a new user
      $user = User::createFromToken($token);

      // Save the user
      $this->em->persist($user);
    }

    // Update last used
    $user->setLastUsed(new DateTime());
    $this->em->flush();

    return $user;
  }

  /** @deprecated */
  public function loadUserByUsername(string $username, $isOidc = false)
  {
    return $this->loadUserByIdentifier($username, $isOidc);
  }

  /**
   * Loads the user for the given username.
   *
   * This method must throw UsernameNotFoundException if the user is not
   * found.
   *
   * @param string $identifier The username
   * @param bool   $isOidc     If set, find Oidc users
   *
   * @return User
   */
  public function loadUserByIdentifier(string $identifier, bool $isOidc = false): UserInterface
  {
    $user = $this->em->getRepository(User::class)
        ->findOneBy(['username' => $identifier, 'isOidc' => $isOidc]);

    if (!$user) {
      throw new UserNotFoundException();
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
   */
  public function refreshUser(UserInterface $user)
  {
    if ($user instanceof User) {
      return $this->loadUserByIdentifier($user->getUserIdentifier(), $user->isOidc());
    } else {
      return $this->loadUserByIdentifier($user->getUserIdentifier());
    }
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
    return $class == User::class;
  }
}
