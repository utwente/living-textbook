<?php

namespace App\Api\Model;

use JMS\Serializer\Annotation\Groups;

class Tag
{
  protected function __construct(
      protected readonly int $id,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $name,
      #[Groups(['Default', 'mutate'])]
      protected readonly string $color,
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

  public function mapToEntity(?\App\Entity\Tag $tag): \App\Entity\Tag
  {
    return ($tag ?? new \App\Entity\Tag())
        ->setName($this->name ?? $tag?->getName() ?? '')
        ->setColor($this->color ?? $tag?->getColor() ?? '');
  }
}
