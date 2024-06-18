<?php

namespace App\Security\Voters;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Request\Wrapper\RequestStudyArea;
use LogicException;
use Override;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudyAreaVoter extends Voter
{
  // Role constants
  final public const string OWNER                = 'STUDYAREA_OWNER';
  final public const string SHOW                 = 'STUDYAREA_SHOW';
  final public const string EDIT                 = 'STUDYAREA_EDIT';
  final public const string REVIEW               = 'STUDYAREA_REVIEW';
  final public const string ANNOTATE             = 'STUDYAREA_ANNOTATE';
  final public const string PRINTER              = 'STUDYAREA_PRINT';
  final public const string ANALYTICS            = 'STUDYAREA_ANALYTICS';
  final public const array SUPPORTED_ATTRIBUTES  = [
    self::OWNER,
    self::SHOW,
    self::EDIT,
    self::REVIEW,
    self::ANNOTATE,
    self::PRINTER,
    self::ANALYTICS,
  ];

  public function __construct(private readonly AccessDecisionManagerInterface $decisionManager)
  {
  }

  #[Override]
  protected function supports(string $attribute, mixed $subject): bool
  {
    if (!in_array($attribute, self::SUPPORTED_ATTRIBUTES)) {
      return false;
    }

    if ($subject !== null && !$subject instanceof StudyArea && !$subject instanceof RequestStudyArea) {
      return false;
    }

    return true;
  }

  #[Override]
  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();

    // Convert anonymous token users
    if (!is_object($user)) {
      $user = null;
    }

    if ($user !== null && !$user instanceof User) {
      // Require null or system user
      return false;
    }

    if ($this->decisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
      return true;
    }

    // Convert study area if required
    if ($subject instanceof RequestStudyArea) {
      // Check for value, otherwise deny access
      if (!$subject->hasValue()) {
        return false;
      }

      $subject = $subject->getStudyArea();
    }

    // Always return false for null values
    if ($subject === null) {
      return false;
    }

    /* @var StudyArea $subject */
    assert($subject instanceof StudyArea);

    switch ($attribute) {
      case self::OWNER:
        return $subject->isOwner($user);
      case self::SHOW:
        return $subject->isVisible($user);
      case self::EDIT:
        return $subject->isEditable($user);
      case self::REVIEW:
        return $subject->isReviewable($user);
      case self::ANNOTATE:
      case self::PRINTER:
        if (!$user) {
          return false;
        }

        return $subject->isVisible($user);
      case self::ANALYTICS:
        return $subject->canViewAnalytics($user);
    }

    throw new LogicException('This code should not be reached!');
  }
}
