<?php

namespace App\Entity\Listener;

use App\Entity\User;
use App\Entity\UserGroupEmail;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

class UserListener
{
  /**
   * This handler ensures that any emailaddresses that have been granted, will receive the new and functional account
   * that can actually be used for rights
   *
   * @ORM\PostPersist()
   *
   * @param User               $user
   * @param LifecycleEventArgs $event
   *
   * @throws \Doctrine\ORM\ORMException
   */
  public function updateStudyAreaRights(User $user, LifecycleEventArgs $event)
  {
    $em = $event->getEntityManager();

    // Find any UserGroupEmail to upgrade
    $userGroupEmails = $em->getRepository(UserGroupEmail::class)->findByEmail($user->getUsername());
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
