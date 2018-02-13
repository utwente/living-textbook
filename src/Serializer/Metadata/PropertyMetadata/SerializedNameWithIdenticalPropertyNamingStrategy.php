<?php

namespace App\Serializer\Metadata\PropertyMetadata;

use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;

class SerializedNameWithIdenticalPropertyNamingStrategy extends IdenticalPropertyNamingStrategy
{
  public function translateName(PropertyMetadata $property)
  {
    return $property->serializedName ?? parent::translateName($property);
  }
}
