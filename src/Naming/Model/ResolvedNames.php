<?php

namespace App\Naming\Model;

use Override;
use Symfony\Component\String\Inflector\InflectorInterface;

readonly class ResolvedNames implements ResolvedNamesInterface
{
  public function __construct(
    public ResolvedConceptNames $concept,
    public ResolvedTermNames $learningOutcome,
    public ResolvedTermNames $skill,
  ) {
  }

  #[Override]
  public function resolvePlurals(InflectorInterface $inflector): void
  {
    $this->concept->resolvePlurals($inflector);
    $this->learningOutcome->resolvePlurals($inflector);
    $this->skill->resolvePlurals($inflector);
  }
}
