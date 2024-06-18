<?php

namespace App\Attribute;

use Attribute;

/**
 * Place this annotation on a method which should not be invoked when a study area is frozen. Handled by
 * App\Subscriber\DenyOnFrozenStudyAreaSubscriber.
 */
#[Attribute(Attribute::TARGET_METHOD)]
class DenyOnFrozenStudyArea
{
  public function __construct(
    public string $route,
    public string $subject,
    public array $routeParams = [],
  ) {
  }
}
