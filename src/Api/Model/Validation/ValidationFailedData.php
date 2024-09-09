<?php

namespace App\Api\Model\Validation;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;

class ValidationFailedData
{
  public function __construct(
    #[Property(default: 'validation.failed')]
    protected readonly string $reason,
    #[Property(type: 'array', items: new Items(new Model(type: ValidationError::class)))]
    protected readonly array $violations,
  ) {
  }
}
