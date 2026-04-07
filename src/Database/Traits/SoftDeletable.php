<?php

namespace App\Database\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait SoftDeletable
{
  #[ORM\Column(name: 'deleted_at', type: Types::DATETIME_MUTABLE, nullable: true)]
  protected ?DateTimeInterface $deletedAt = null;

  #[ORM\Column(name: 'deleted_by', type: Types::STRING, length: 255, nullable: true)]
  protected ?string $deletedBy = null;

  public function setDeletedAt(?DateTime $deletedAt = null): self
  {
    $this->deletedAt = $deletedAt;

    return $this;
  }

  public function getDeletedAt(): ?DateTimeInterface
  {
    return $this->deletedAt;
  }

  public function setDeletedBy(?string $deletedBy): self
  {
    $this->deletedBy = $deletedBy;

    return $this;
  }

  public function getDeletedBy(): ?string
  {
    return $this->deletedBy;
  }

  public function isDeleted(): bool
  {
    return null !== $this->deletedAt;
  }
}
