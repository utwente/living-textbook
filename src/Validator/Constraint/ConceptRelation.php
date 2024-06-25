<?php

namespace App\Validator\Constraint;

use Attribute;
use Override;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
class ConceptRelation extends Constraint
{
  public function __construct(
    /** Error when a relation is made twice. */
    public string $duplicatedRelation = 'concept.duplicated-relation',
    /**
     * Error when a relation is mirrored
     *   a -> b
     *   b <- a.
     */
    public string $inversedRelation = 'concept.inversed-relation',
    mixed $options = null,
    ?array $groups = null,
    mixed $payload = null)
  {
    parent::__construct($options, $groups, $payload);
  }

  /** Sets this validator as class validator. */
  #[Override]
  public function getTargets(): string
  {
    return self::CLASS_CONSTRAINT;
  }
}
