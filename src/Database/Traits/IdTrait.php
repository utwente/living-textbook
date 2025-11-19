<?php

namespace App\Database\Traits;

use Doctrine\ORM\Mapping as ORM;
use Drenso\Shared\Exception\NullGuard\MustNotBeNullException;
use JMS\Serializer\Annotation as Serializer;

trait IdTrait
{
  #[Serializer\Expose]
  #[Serializer\Groups(['Default', 'review_change', 'id_only'])]
  #[Serializer\Type('int')]
  #[ORM\Column]
  #[ORM\Id]
  #[ORM\GeneratedValue]
  private ?int $id = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getNonNullId(): int
  {
    return $this->id ?? throw new MustNotBeNullException();
  }
}
