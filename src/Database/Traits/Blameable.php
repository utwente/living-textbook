<?php

namespace App\Database\Traits;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Blameable
{
  #[ORM\Column(name: 'created_at', type: Types::DATETIME_MUTABLE, nullable: false)]
  #[Gedmo\Timestampable(on: 'create')]
  private ?DateTimeInterface $createdAt = null;

  #[ORM\Column(name: 'created_by', type: Types::STRING, length: 255, nullable: true)]
  #[Gedmo\Blameable(on: 'create')]
  private ?string $createdBy = null;

  #[ORM\Column(name: 'updated_at', type: Types::DATETIME_MUTABLE, nullable: true)]
  #[Gedmo\Timestampable(on: 'update')]
  private ?DateTimeInterface $updatedAt = null;

  #[ORM\Column(name: 'updated_by', type: Types::STRING, length: 255, nullable: true)]
  #[Gedmo\Blameable(on: 'update')]
  private ?string $updatedBy = null;

  /** Get the last update time, which is either creation time or update time */
  public function getLastUpdated(): ?DateTimeInterface
  {
    return $this->getUpdatedAt() ?? $this->getCreatedAt();
  }

  /** Get the last updated by, which is either creation by or update by */
  public function getLastUpdatedBy(): ?string
  {
    return $this->getUpdatedBy() ?? $this->getCreatedBy();
  }

  public function setCreatedAt(DateTime $createdAt): self
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  public function getCreatedAt(): ?DateTimeInterface
  {
    return $this->createdAt;
  }

  public function setCreatedBy(?string $createdBy): self
  {
    $this->createdBy = $createdBy;

    return $this;
  }

  public function getCreatedBy(): ?string
  {
    return $this->createdBy;
  }

  public function setUpdatedAt(?DateTime $updatedAt): self
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  public function getUpdatedAt(): ?DateTimeInterface
  {
    return $this->updatedAt;
  }

  public function setUpdatedBy(?string $updatedBy): self
  {
    $this->updatedBy = $updatedBy;

    return $this;
  }

  public function getUpdatedBy(): ?string
  {
    return $this->updatedBy;
  }
}
