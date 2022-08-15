<?php

namespace App\Entity\Listener;

use App\Entity\User;
use App\Entity\UserGroupEmail;
use App\Repository\UserGroupEmailRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMException;

class UserListener
{
  /**
   * This handler ensures that any e-mail addresses that have been granted, will receive the new and functional account
   * that can actually be used for rights.
   *
   * @ORM\PostPersist()
   *
   * @throws ORMException
   */
  public function updateStudyAreaRights(User $user, LifecycleEventArgs $event)
  {
    $em = $event->getObjectManager();

    // Find any UserGroupEmail to upgrade
    $repo = $em->getRepository(UserGroupEmail::class);
    assert($repo instanceof UserGroupEmailRepository);
    $userGroupEmails = $repo->findByEmail($user->getUserIdentifier());
    if (count($userGroupEmails) == 0) {
      return;
    }

    // Upgrade them to full user members for the user group
    foreach ($userGroupEmails as $userGroupEmail) {
      $userGroupEmail->getUserGroup()->getUsers()->add($user);
      $em->remove($userGroupEmail);
    }

    // Save changes
    $em->flush();
  }
}
