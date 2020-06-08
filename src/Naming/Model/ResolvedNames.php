<?php

namespace App\Naming\Model;

class ResolvedNames
{
  /** @var ResolvedConceptNames */
  private $concept;

  public function __construct(ResolvedConceptNames $concept)
  {
    $this->concept = $concept;
  }

  /**
   * @return ResolvedConceptNames
   */
  public function concept(): ResolvedConceptNames
  {
    return $this->concept;
  }
}
