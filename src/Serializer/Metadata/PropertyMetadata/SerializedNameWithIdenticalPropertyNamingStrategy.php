<?php

namespace App\Serializer\Metadata\PropertyMetadata;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Override;

class SerializedNameWithIdenticalPropertyNamingStrategy implements PropertyNamingStrategyInterface
{
  #[Override]
  public function translateName(PropertyMetadata $property): string
  {
    return $property->serializedName ?? $property->name;
  }
}
