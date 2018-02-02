<?php

namespace App\Validator\Constraint\Data;

use Symfony\Component\Validator\Constraint;

/**
 * Class WordCount
 *
 * @author BobV
 *
 * @Annotation
 */
class WordCount extends Constraint
{
  /**
   * Minimum word count
   *
   * @var int
   */
  public $min = 10;

  /**
   * Maximum word count
   *
   * @var int
   */
  public $max = 1000;

  /**
   * Minimum word count error message
   *
   * @var string
   */
  public $minMessage = 'data.word-count-min';

  /**
   * Maximum word count error message
   *
   * @var string
   */
  public $maxMessage = 'data.word-count-max';

}
