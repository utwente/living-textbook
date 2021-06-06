<?php

namespace App\Analytics\Model;

use App\Entity\StudyArea;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SynthesizeRequest
{
  /**
   * @var StudyArea
   */
  private $studyArea;

  /**
   * Number of users to skip
   *
   * @var integer
   *
   * @Assert\NotNull()
   * @Assert\Range(min=0, max=200)
   */
  public $usersIgnore = 10;

  /**
   * Number of perfect followers
   *
   * @var integer
   *
   * @Assert\NotNull()
   * @Assert\Range(min=0, max=200)
   */
  public $usersPerfect = 20;

  /**
   * Number of flawed path followers
   *
   * @var integer
   *
   * @Assert\NotNull()
   * @Assert\Range(min=0, max=200)
   */
  public $usersFlawed = 150;

  /**
   * Number of test followers of the flawed path followers
   *
   * @var integer
   *
   * @Assert\NotNull()
   * @Assert\Range(min=0, max=200)
   * @Assert\LessThan(propertyPath="usersFlawed")
   */
  public $usersFlawedTest = 130;

  /**
   * Number of concept browsers followers
   *
   * @var integer
   *
   * @Assert\NotNull()
   * @Assert\Range(min=0, max=200)
   */
  public $usersConceptBrowsers = 30;

  /**
   * Number of test followers of the concept browsers followers
   *
   * @var integer
   *
   * @Assert\NotNull()
   * @Assert\Range(min=0, max=200)
   * @Assert\LessThan(propertyPath="usersConceptBrowsers")
   */
  public $usersConceptBrowsersTest = 27;

  /**
   * Number of test followers (not including the flawed and conceptBrowsers)
   *
   * @var integer
   *
   * @Assert\NotNull()
   * @Assert\Range(min=0, max=200)
   */
  public $usersTest = 100;

  /**
   * @var DateTimeImmutable|false
   *
   * @Assert\NotNull()
   */
  public $testMoment;

  /**
   * Probability that a flawed path follower stops after each concept
   *
   * @var float
   *
   * @Assert\NotNull
   * @Assert\Range(min=0, max=1)
   */
  public $flawedDropOffChance = 0.04;

  /**
   * Probability that a concept browser follower stops after each concept
   *
   * @var float
   *
   * @Assert\NotNull
   * @Assert\Range(min=0, max=1)
   */
  public $conceptBrowserDropOffChance = 0.08;

  /**
   * The amount of days between the teaching moment of each learning path
   *
   * @var int
   *
   * @Assert\NotNull
   * @Assert\Range(min=1, max=31)
   */
  public $daysBetweenLearningPaths = 7;

  /**
   * The amount of days between the last learning path and the test moment
   *
   * @var int
   *
   * @Assert\NotNull
   * @Assert\Range(min=1, max=31)
   */
  public $daysBeforeTest = 7;

  public function __construct(StudyArea $studyArea)
  {
    $this->testMoment = (new DateTimeImmutable())->modify('-1 day')->setTime(14, 30);
    $this->studyArea  = $studyArea;
  }

  /**
   * @Assert\Callback()
   */
  public function validate(ExecutionContextInterface $context)
  {
    if ($this->testMoment >= new DateTimeImmutable()) {
      $context->buildViolation('analytics.before-now')
          ->atPath('testMoment')
          ->addViolation();
    }
  }

  public function getSettings(bool $debug, string $host): array
  {
    return [
        'debug'                               => $debug,
        'userGenerationSettings'              => [
            'debug'           => $debug,
            'ignore'          => $this->usersIgnore,
            'perfect'         => $this->usersPerfect,
            'flawed'          => [$this->usersFlawed, $this->usersFlawedTest],
            'conceptBrowsers' => [$this->usersConceptBrowsers, $this->usersConceptBrowsersTest],
            'test'            => $this->usersTest,
            'basis'           => ['synthetic-data+', '@' . $host],
        ],
        'studyArea'                           => $this->studyArea->getId(),
        'pathFollowerPath'                    => [
            'dropOffChance' => $this->flawedDropOffChance,
        ],
        'conceptbrowserFollowerdropOffChance' => $this->conceptBrowserDropOffChance,
        'testMoment'                          => $this->testMoment->format('Y-m-d H:i:s'),
        'learningpaths'                       => [],
        'conceptData'                         => [],
    ];
  }
}
