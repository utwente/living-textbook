<?php

namespace App\Naming\Model;

use Symfony\Component\String\Inflector\InflectorInterface;

class ResolvedConceptNames implements ResolvedNamesInterface
{
  /** @var string */
  private $definition;
  /** @var string */
  private $examples;
  /** @var string */
  private $howTo;
  /** @var string */
  private $introduction;
  /** @var string */
  private $priorKnowledge;
  /** @var string */
  private $selfAssessment;
  /** @var string */
  private $synonyms;
  /** @var string */
  private $theoryExplanation;

  public function __construct(
      string $definition, string $introduction, string $synonyms, string $priorKnowledge, string $theoryExplanation,
      string $howTo, string $examples, string $selfAssessment)
  {
    $this->definition        = strtolower($definition);
    $this->introduction      = strtolower($introduction);
    $this->synonyms          = strtolower($synonyms);
    $this->priorKnowledge    = strtolower($priorKnowledge);
    $this->theoryExplanation = strtolower($theoryExplanation);
    $this->howTo             = strtolower($howTo);
    $this->examples          = strtolower($examples);
    $this->selfAssessment    = strtolower($selfAssessment);
  }

  public function resolvePlurals(InflectorInterface $inflector)
  {
    // Nothing to do here
  }

  /**
   * @return string
   */
  public function definition(): string
  {
    return $this->definition;
  }

  /**
   * @return string
   */
  public function examples(): string
  {
    return $this->examples;
  }

  /**
   * @return string
   */
  public function howTo(): string
  {
    return $this->howTo;
  }

  /**
   * @return string
   */
  public function introduction(): string
  {
    return $this->introduction;
  }

  /**
   * @return string
   */
  public function priorKnowledge(): string
  {
    return $this->priorKnowledge;
  }

  /**
   * @return string
   */
  public function selfAssessment(): string
  {
    return $this->selfAssessment;
  }

  /**
   * @return string
   */
  public function synonyms(): string
  {
    return $this->synonyms;
  }

  /**
   * @return string
   */
  public function theoryExplanation(): string
  {
    return $this->theoryExplanation;
  }
}
