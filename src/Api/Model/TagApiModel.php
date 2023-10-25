<?php

namespace App\Api\Model;

use App\Entity\Tag;
use JMS\Serializer\Annotation\Groups;

class TagApiModel
{
  protected function __construct(
    protected readonly int $id,
    #[Groups(['Default', 'mutate'])]
    protected readonly string $name,
    #[Groups(['Default', 'mutate'])]
    protected readonly string $color,
  ) {
  }

  public static function fromEntity(Tag $tag): self
  {
    return new self(
      $tag->getId(),
      $tag->getName(),
      $tag->getColor()
    );
  }

  public function mapToEntity(?Tag $tag): Tag
  {
    return ($tag ?? new Tag())
      ->setName($this->name ?? $tag?->getName() ?? '')
      ->setColor($this->color ?? $tag?->getColor() ?? '');
  }
}
