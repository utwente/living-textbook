<?php

namespace App\Database\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Blameable
{
  /** @var DateTime */
  #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
  #[Gedmo\Timestampable(on: 'create')]
  private $createdAt;

  /** @var string */
  #[ORM\Column(name: 'created_by', type: 'string', length: 255, nullable: true)]
  #[Gedmo\Blameable(on: 'create')]
  private $createdBy;

  /** @var DateTime */
  #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: true)]
  #[Gedmo\Timestampable(on: 'update')]
  private $updatedAt;

  /** @var string */
  #[ORM\Column(name: 'updated_by', type: 'string', length: 255, nullable: true)]
  #[Gedmo\Blameable(on: 'update')]
  private $updatedBy;

  /** Get the last update time, which is either creation time or update time */
  public function getLastUpdated()
  {
    return $this->getUpdatedAt() ?? $this->getCreatedAt();
  }

  /** Get the last updated by, which is either creation by or update by */
  public function getLastUpdatedBy()
  {
    return $this->getUpdatedBy() ?? $this->getCreatedBy();
  }

  /**
   * Set createdAt.
   *
   * @param DateTime $createdAt
   *
   * @return $this
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get createdAt.
   *
   * @return DateTime
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * Set createdBy.
   *
   * @param string $createdBy
   *
   * @return $this
   */
  public function setCreatedBy($createdBy)
  {
    $this->createdBy = $createdBy;

    return $this;
  }

  /**
   * Get createdBy.
   *
   * @return string
   */
  public function getCreatedBy()
  {
    return $this->createdBy;
  }

  /**
   * Set updatedAt.
   *
   * @param DateTime $updatedAt
   *
   * @return $this
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  /**
   * Get updatedAt.
   *
   * @return DateTime
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * Set updatedBy.
   *
   * @param string $updatedBy
   *
   * @return $this
   */
  public function setUpdatedBy($updatedBy)
  {
    $this->updatedBy = $updatedBy;

    return $this;
  }

  /**
   * Get updatedBy.
   *
   * @return string
   */
  public function getUpdatedBy()
  {
    return $this->updatedBy;
  }
}
