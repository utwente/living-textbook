<?php

namespace App\Api\Model\Validation;

use OpenApi\Attributes\Property;

class ValidationError
{
  #[Property(property: 'property_path')]
  public readonly string $propertyPath;
  public readonly string $message;
}
