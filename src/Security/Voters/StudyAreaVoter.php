<?php

namespace App\Security\Voters;

use App\Entity\StudyArea;
use App\Entity\User;
use App\Request\Wrapper\RequestStudyArea;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class StudyAreaVoter extends Voter
{
  // Role constants
  public const OWNER                = 'STUDYAREA_OWNER';
  public const SHOW                 = 'STUDYAREA_SHOW';
  public const EDIT                 = 'STUDYAREA_EDIT';
  public const REVIEW               = 'STUDYAREA_REVIEW';
  public const ANNOTATE             = 'STUDYAREA_ANNOTATE';
  public const PRINTER              = 'STUDYAREA_PRINT';
  public const ANALYTICS            = 'STUDYAREA_ANALYTICS';
  public const SUPPORTED_ATTRIBUTES = [
      self::OWNER,
      self::SHOW,
      self::EDIT,
      self::REVIEW,
      self::ANNOTATE,
      self::PRINTER,
      self::ANALYTICS,
  ];

  /** @var AccessDecisionManagerInterface */
  private $decisionManager;

  /** StudyAreaVoter constructor. */
  public function __construct(AccessDecisionManagerInterface $decisionManager)
  {
    $this->decisionManager = $decisionManager;
  }

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
    if (!in_array($attribute, self::SUPPORTED_ATTRIBUTES)) {
      return false;
    }

    if ($subject !== null && !$subject instanceof StudyArea && !$subject instanceof RequestStudyArea) {
      return false;
    }

    return true;
  }

  /**
   * Perform a single access check operation on a given attribute, subject and token.
   * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
   *
   * @param string $attribute
   * @param mixed  $subject
   *
   * @return bool
   */
  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
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

    /* @var StudyArea $subject */
    assert($subject instanceof StudyArea);

    // Always return false for null values
    if ($subject === null) {
      return false;
    }

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
