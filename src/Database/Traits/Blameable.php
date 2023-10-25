<?php

namespace App\Database\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

trait Blameable
{
  /**
   * @var DateTime
   *
   * @Gedmo\Timestampable(on="create")
   *
   * @ORM\Column(name="created_at", type="datetime", nullable=false)
   */
  private $createdAt;

  /**
   * @var string
   *
   * @Gedmo\Blameable(on="create")
   *
   * @ORM\Column(name="created_by", type="string", length=255, nullable=true)
   */
  private $createdBy;

  /**
   * @var DateTime
   *
   * @Gedmo\Timestampable(on="update")
   *
   * @ORM\Column(name="updated_at", type="datetime", nullable=true)
   */
  private $updatedAt;

  /**
   * @var string
   *
   * @Gedmo\Blameable(on="update")
   *
   * @ORM\Column(name="updated_by", type="string", length=255, nullable=true)
   */
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
