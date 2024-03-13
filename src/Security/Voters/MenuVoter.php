<?php

namespace App\Security\Voters;

use App\Entity\StudyArea;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Repository\LearningPathRepository;
use App\Request\Wrapper\RequestStudyArea;
use Doctrine\ORM\NonUniqueResultException;
use Override;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class MenuVoter.
 *
 * This voter will decide whether the menu item is visible for you.
 * An item is visible when:
 *  - You have edit rights for the study area
 *  - You have study area show access & the content type has actual content to be shown
 */
class MenuVoter extends Voter
{
  final public const string CONCEPTS             = 'MENU_CONCEPTS';
  final public const string LEARNING_PATHS       = 'MENU_LEARNING_PATHS';
  final public const string ABBREVIATIONS        = 'MENU_ABBREVIATIONS';
  final public const string CONTRIBUTORS         = 'MENU_CONTRIBUTORS';
  final public const string EXTERNAL_RESOURCES   = 'MENU_EXTERNAL_RESOURCES';
  final public const string LEARNING_OUTCOMES    = 'MENU_LEARNING_OUTCOMES';
  final public const array SUPPORTED_ATTRIBUTES  = [
    self::CONCEPTS,
    self::LEARNING_PATHS,
    self::ABBREVIATIONS,
    self::CONTRIBUTORS,
    self::EXTERNAL_RESOURCES,
    self::LEARNING_OUTCOMES,
  ];
  private AbbreviationRepository $abbreviationRepository;
  private ConceptRepository $conceptRepository;
  private ContributorRepository $contributorRepository;
  private AccessDecisionManagerInterface $decisionManager;
  private ExternalResourceRepository $externalResourceRepository;
  private LearningOutcomeRepository $learningOutcomeRepository;
  private LearningPathRepository $learningPathRepository;

  public function __construct(
    AccessDecisionManagerInterface $decisionManager, ConceptRepository $conceptRepository,
    LearningPathRepository $learningPathRepository, AbbreviationRepository $abbreviationRepository,
    ContributorRepository $contributorRepository, ExternalResourceRepository $externalResourceRepository,
    LearningOutcomeRepository $learningOutcomeRepository)
  {
    $this->decisionManager            = $decisionManager;
    $this->conceptRepository          = $conceptRepository;
    $this->learningPathRepository     = $learningPathRepository;
    $this->abbreviationRepository     = $abbreviationRepository;
    $this->contributorRepository      = $contributorRepository;
    $this->externalResourceRepository = $externalResourceRepository;
    $this->learningOutcomeRepository  = $learningOutcomeRepository;
  }

  #[Override]
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

  /** @throws NonUniqueResultException */
  #[Override]
  protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
  {
    // Always allow users who can edit the area
    if ($this->decisionManager->decide($token, ['STUDYAREA_EDIT'], $subject)) {
      return true;
    }

    // Disallow users who cannot even see this area
    if (!$this->decisionManager->decide($token, ['STUDYAREA_SHOW'], $subject)) {
      return false;
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

    return match ($attribute) {
      self::CONCEPTS           => $this->conceptRepository->getCountForStudyArea($subject) > 0,
      self::LEARNING_PATHS     => $this->learningPathRepository->getCountForStudyArea($subject) > 0,
      self::ABBREVIATIONS      => $this->abbreviationRepository->getCountForStudyArea($subject) > 0,
      self::CONTRIBUTORS       => $this->contributorRepository->getCountForStudyArea($subject) > 0,
      self::EXTERNAL_RESOURCES => $this->externalResourceRepository->getCountForStudyArea($subject) > 0,
      self::LEARNING_OUTCOMES  => $this->learningOutcomeRepository->getCountForStudyArea($subject) > 0,
      default                  => throw new RuntimeException('This code should not be reached!'),
    };
  }
}
