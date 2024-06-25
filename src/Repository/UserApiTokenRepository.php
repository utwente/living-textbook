<?php

namespace App\Repository;

use App\Entity\UserApiToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Override;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserApiTokenRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, UserApiToken::class);
  }

  #[Override]
  public function upgradePassword(PasswordAuthenticatedUserInterface|UserInterface $user, string $newHashedPassword): void
  {
    if (!$user instanceof UserApiToken) {
      throw new AuthenticationException('Invalid user class');
    }

    $user->setPassword($newHashedPassword);
    $this->_em->flush();
  }
}
