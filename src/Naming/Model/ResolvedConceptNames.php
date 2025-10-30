<?php

namespace App\Naming\Model;

use Override;
use Symfony\Component\String\Inflector\InflectorInterface;

use function Symfony\Component\String\u;

class ResolvedConceptNames implements ResolvedNamesInterface
{
  private string $definition;
  private string $examples;
  private string $howTo;
  private string $introduction;
  private string $priorKnowledge;
  private string $selfAssessment;
  private string $synonyms;
  private string $theoryExplanation;

  public function __construct(
    string $definition,
    string $introduction,
    string $synonyms,
    string $priorKnowledge,
    string $theoryExplanation,
    string $howTo,
    string $examples,
    string $selfAssessment,
  ) {
    $this->definition        = u($definition)->lower();
    $this->introduction      = u($introduction)->lower();
    $this->synonyms          = u($synonyms)->lower();
    $this->priorKnowledge    = u($priorKnowledge)->lower();
    $this->theoryExplanation = u($theoryExplanation)->lower();
    $this->howTo             = u($howTo)->lower();
    $this->examples          = u($examples)->lower();
    $this->selfAssessment    = u($selfAssessment)->lower();
  }

  #[Override]
  public function resolvePlurals(InflectorInterface $inflector): void
  {
    // Nothing to do here
  }

  public function definition(bool $titleCase = false): string
  {
    return $titleCase ? u($this->definition)->title() : $this->definition;
  }

  public function examples(bool $titleCase = false): string
  {
    return $titleCase ? u($this->definition())->title() : $this->examples;
  }

  public function howTo(bool $titleCase = false): string
  {
    return $titleCase ? u($this->howTo)->title() : $this->howTo;
  }

  public function introduction(bool $titleCase = false): string
  {
    return $titleCase ? u($this->introduction)->title() : $this->introduction;
  }

  public function priorKnowledge(bool $titleCase = false): string
  {
    return $titleCase ? u($this->priorKnowledge)->title() : $this->priorKnowledge;
  }

  public function selfAssessment(bool $titleCase = false): string
  {
    return $titleCase ? u($this->selfAssessment)->title() : $this->selfAssessment;
  }

  public function synonyms(bool $titleCase = false): string
  {
    return $titleCase ? u($this->synonyms)->title() : $this->synonyms;
  }

  public function theoryExplanation(bool $titleCase = false): string
  {
    return $titleCase ? u($this->theoryExplanation)->title() : $this->theoryExplanation;
  }
}
