<?php

namespace App\Validator\Constraint\Data;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_PROPERTY)]
class WordCount extends Constraint
{
  public function __construct(
    /** Minimum word count. */
    public int $min = 10,
    /** Maximum word count. */
    public int $max = 1000,
    /** Minimum word count error message. */
    public string $minMessage = 'data.word-count-min',
    /** Maximum word count error message. */
    public string $maxMessage = 'data.word-count-max',
    mixed $options = null,
    ?array $groups = null,
    mixed $payload = null)
  {
    parent::__construct($options, $groups, $payload);
  }
}
