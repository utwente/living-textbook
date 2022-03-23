<?php

namespace App\Naming\Model;

use Symfony\Component\String\Inflector\InflectorInterface;

class ResolvedNames implements ResolvedNamesInterface
{
  /** @var ResolvedConceptNames */
  private $concept;

  /** @var ResolvedLearningOutcomeNames */
  private $learningOutcome;

  public function __construct(ResolvedConceptNames $concept, ResolvedLearningOutcomeNames $learningOutcome)
  {
    $this->concept         = $concept;
    $this->learningOutcome = $learningOutcome;
  }

  public function resolvePlurals(InflectorInterface $inflector)
  {
    $this->concept->resolvePlurals($inflector);
    $this->learningOutcome->resolvePlurals($inflector);
  }

  /** @return ResolvedConceptNames */
  public function concept(): ResolvedConceptNames
  {
    return $this->concept;
  }

  /** @return ResolvedLearningOutcomeNames */
  public function learningOutcome(): ResolvedLearningOutcomeNames
  {
    return $this->learningOutcome;
  }
}
