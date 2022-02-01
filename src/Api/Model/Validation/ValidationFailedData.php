<?php

namespace App\Api\Model\Validation;

use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\Property;

class ValidationFailedData
{
  #[Property(default: 'validation.failed')]
  public readonly string $reason;
  #[Property(type: 'array', items: new Items(new Model(type: ValidationError::class)))]
  public readonly array $violations;
}
