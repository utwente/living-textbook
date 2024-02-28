<?php

namespace App\Naming\Model;

use Override;
use Symfony\Component\String\Inflector\InflectorInterface;

class ResolvedNames implements ResolvedNamesInterface
{
  private ResolvedConceptNames $concept;

  private ResolvedLearningOutcomeNames $learningOutcome;

  public function __construct(ResolvedConceptNames $concept, ResolvedLearningOutcomeNames $learningOutcome)
  {
    $this->concept         = $concept;
    $this->learningOutcome = $learningOutcome;
  }

  #[Override]
  public function resolvePlurals(InflectorInterface $inflector)
  {
    $this->concept->resolvePlurals($inflector);
    $this->learningOutcome->resolvePlurals($inflector);
  }

  public function concept(): ResolvedConceptNames
  {
    return $this->concept;
  }

  public function learningOutcome(): ResolvedLearningOutcomeNames
  {
    return $this->learningOutcome;
  }
}
