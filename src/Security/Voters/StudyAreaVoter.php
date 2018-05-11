<?php

namespace App\Security\Voters;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Request\Wrapper\RequestStudyArea;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudyAreaVoter extends Voter
{

  const OWNER = 'STUDYAREA_OWNER';

  /**
   * Determines if the attribute and subject are supported by this voter.
   *
   * @param string $attribute An attribute
   * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
   *
   * @return bool True if the attribute and subject are supported, false otherwise
   */
  protected function supports($attribute, $subject)
  {
    if (!in_array($attribute, $this->supportedAttributes())) {
      return false;
    }

    if (!$subject instanceof StudyArea && !$subject instanceof RequestStudyArea) {
      return false;
    }

    return true;
  }

  /**
   * Perform a single access check operation on a given attribute, subject and token.
   * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
   *
   * @param string         $attribute
   * @param mixed          $subject
   * @param TokenInterface $token
   *
   * @return bool
   */
  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {
    $user = $token->getUser();

    if (!$user instanceof User) {
      // Require authenticated user
      return false;
    }

    // Convert study area if required
    if ($subject instanceof RequestStudyArea) {
      $subject = $subject->getStudyArea();
    }

    assert($subject instanceof StudyArea);

    switch ($attribute) {
      case self::OWNER:
        return $this->isOwner($user, $subject);
    }

    throw new \LogicException('This code should not be reached!');
  }

  /**
   * @return array
   */
  private function supportedAttributes()
  {
    return [
        self::OWNER,
    ];
  }

  /**
   * Check whether the given user is the owner
   *
   * @param User      $user
   * @param StudyArea $studyArea
   *
   * @return bool
   */
  private function isOwner(User $user, StudyArea $studyArea)
  {
    return $user->getId() === $studyArea->getOwner()->getId();
  }
}
