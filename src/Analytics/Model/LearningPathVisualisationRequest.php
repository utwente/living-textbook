<?php

namespace App\Analytics\Model;

use App\Entity\LearningPath;
use DateTime;
use Symfony\Component\Validator\Constraints as Assert;

class LearningPathVisualisationRequest
{
  /**
   * @var LearningPath
   *
   * @Assert\NotNull()
   */
  public $learningPath;

  /**
   * @var DateTime
   *
   * @Assert\NotNull()
   * @Assert\Type("datetime")
   * @Assert\GreaterThanOrEqual(propertyPath="periodStart")
   * @Assert\LessThan(propertyPath="periodEnd")
   */
  public $teachingMoment;

  /**
   * @var DateTime
   *
   * @Assert\NotNull()
   * @Assert\Type("datetime")
   * @Assert\LessThan(propertyPath="periodEnd")
   */
  public $periodStart;

  /**
   * @var DateTime
   *
   * @Assert\NotNull()
   * @Assert\Type("datetime")
   */
  public $periodEnd;

  /**
   * @var bool
   *
   * @Assert\NotNull()
   */
  public $forceRebuild;

  /**
   * LearningPathVisualisationRequest constructor.
   *
   * Fills the object with some default data
   */
  public function __construct()
  {
    $date = new DateTime();
    $date->setTime(0, 0, 0);

    $this->teachingMoment = (clone $date)->modify('-2 week');
    $this->periodStart    = (clone $this->teachingMoment)->modify('-1 day');
    $this->periodEnd      = (clone $date)->modify('+1 day');

    $this->forceRebuild = false;
  }
}
