<?php

namespace App\Validator\Constraint;

use Override;
use Symfony\Component\Validator\Constraint;

/**
 * Class ConceptRelation.
 *
 * @author BobV
 *
 * @Annotation
 */
class ConceptRelation extends Constraint
{
  /**
   * Error when a relation is made twice.
   *
   * @var string
   */
  public $duplicatedRelation = 'concept.duplicated-relation';

  /**
   * Error when a relation is mirrored
   *   a -> b
   *   b <- a.
   *
   * @var string
   */
  public $inversedRelation = 'concept.inversed-relation';

  /** Sets this validator as class validator. */
  #[Override]
  public function getTargets(): array|string
  {
    return self::CLASS_CONSTRAINT;
  }
}
