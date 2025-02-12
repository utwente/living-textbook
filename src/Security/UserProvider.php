<?php

namespace App\Security;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Drenso\OidcBundle\Model\OidcTokens;
use Drenso\OidcBundle\Model\OidcUserData;
use Drenso\OidcBundle\Security\UserProvider\OidcUserProviderInterface;
use Override;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/** @implements OidcUserProviderInterface<User> */
class UserProvider implements OidcUserProviderInterface
{
  public function __construct(private readonly EntityManagerInterface $em)
  {
  }

  #[Override]
  public function ensureUserExists(string $userIdentifier, OidcUserData $userData, OidcTokens $tokens): void
  {
    // Determine whether this user already exists
    try {
      $user = $this->loadUserByIdentifier($userIdentifier, true);
      $user->update($userData);
    } catch (UserNotFoundException) {
      // Create a new user
      $user = User::createFromOidcUserData($userData);

      // Save the user
      $this->em->persist($user);
    }

    // Update last used
    $user->setLastUsed(new DateTime());
    $this->em->flush();
  }

  #[Override]
  public function loadOidcUser(string $userIdentifier): UserInterface
  {
    return $this->loadUserByIdentifier($userIdentifier, true);
  }

  /**
   * Loads the user for the given username.
   *
   * This method must throw UsernameNotFoundException if the user is not
   * found.
   *
   * @param string $identifier The username
   * @param bool   $isOidc     If set, find Oidc users
   */
  #[Override]
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
   */
  #[Override]
  public function refreshUser(UserInterface $user): UserInterface
  {
    if ($user instanceof User) {
      return $this->loadUserByIdentifier($user->getUserIdentifier(), $user->isOidc());
    } else {
      return $this->loadUserByIdentifier($user->getUserIdentifier());
    }
  }

  /** Whether this provider supports the given user class. */
  #[Override]
  public function supportsClass(string $class): bool
  {
    return $class == User::class;
  }
}
