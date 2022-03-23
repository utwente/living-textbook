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
  public const CONCEPTS             = 'MENU_CONCEPTS';
  public const LEARNING_PATHS       = 'MENU_LEARNING_PATHS';
  public const ABBREVIATIONS        = 'MENU_ABBREVIATIONS';
  public const CONTRIBUTORS         = 'MENU_CONTRIBUTORS';
  public const EXTERNAL_RESOURCES   = 'MENU_EXTERNAL_RESOURCES';
  public const LEARNING_OUTCOMES    = 'MENU_LEARNING_OUTCOMES';
  public const SUPPORTED_ATTRIBUTES = [
      self::CONCEPTS,
      self::LEARNING_PATHS,
      self::ABBREVIATIONS,
      self::CONTRIBUTORS,
      self::EXTERNAL_RESOURCES,
      self::LEARNING_OUTCOMES,
  ];
  /** @var AbbreviationRepository */
  private $abbreviationRepository;
  /** @var ConceptRepository */
  private $conceptRepository;
  /** @var ContributorRepository */
  private $contributorRepository;
  /** @var AccessDecisionManagerInterface */
  private $decisionManager;
  /** @var ExternalResourceRepository */
  private $externalResourceRepository;
  /** @var LearningOutcomeRepository */
  private $learningOutcomeRepository;
  /** @var LearningPathRepository */
  private $learningPathRepository;

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

  /** {@inheritDoc} */
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
   * {@inheritDoc}
   *
   * @throws NonUniqueResultException
   */
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

    switch ($attribute) {
      case self::CONCEPTS:
        return $this->conceptRepository->getCountForStudyArea($subject) > 0;
      case self::LEARNING_PATHS:
        return $this->learningPathRepository->getCountForStudyArea($subject) > 0;
      case self::ABBREVIATIONS:
        return $this->abbreviationRepository->getCountForStudyArea($subject) > 0;
      case self::CONTRIBUTORS:
        return $this->contributorRepository->getCountForStudyArea($subject) > 0;
      case self::EXTERNAL_RESOURCES:
        return $this->externalResourceRepository->getCountForStudyArea($subject) > 0;
      case self::LEARNING_OUTCOMES:
        return $this->learningOutcomeRepository->getCountForStudyArea($subject) > 0;
    }

    throw new RuntimeException('This code should not be reached!');
  }
}
