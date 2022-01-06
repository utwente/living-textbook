<?php

namespace App\Api\Model;

class Tag
{
  protected function __construct(
      public readonly int $id,
      public readonly string $name,
      public readonly string $color,
  )
  {
  }

  public static function fromEntity(\App\Entity\Tag $tag): self
  {
    return new self(
        $tag->getId(),
        $tag->getName(),
        $tag->getColor()
    );
  }
}
