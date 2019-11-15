<?php

namespace App\Serializer\Metadata\PropertyMetadata;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;

class SerializedNameWithIdenticalPropertyNamingStrategy implements PropertyNamingStrategyInterface
{
  public function translateName(PropertyMetadata $property): string
  {
    return $property->serializedName ?? $property->name;
  }
}
