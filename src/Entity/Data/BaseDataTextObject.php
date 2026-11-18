<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

trait BaseDataTextObject
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  #[ORM\Column(name: 'text', type: Types::TEXT, nullable: true)]
  #[Serializer\Groups(['review_change'])]
  #[Serializer\Type('string')]
  private ?string $text;

  /** Determine whether this block has data. */
  public function hasData(): bool
  {
    return $this->text !== null && $this->text != '';
  }

  public function getText(): ?string
  {
    return $this->text;
  }

  public function setText(?string $text): self
  {
    $this->text = $text;

    return $this;
  }
}
