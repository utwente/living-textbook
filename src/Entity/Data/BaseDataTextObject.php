<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class DataLearningOutcomes
 *
 * @author BobV
 *
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
trait BaseDataTextObject
{
  use IdTrait;
  use Blameable;
  use SoftDeletable;

  /**
   * Learning outcomes
   *
   * @var string|null
   *
   * @ORM\Column(name="text", type="text", nullable=true)
   */
  private $text;

  /**
   * Determine whether this block has data
   *
   * @return bool
   */
  function hasData(): bool
  {
    return $this->text !== NULL && $this->text != '';
  }

  /**
   * @return string|null
   */
  public function getText(): ?string
  {
    return $this->text;
  }

  /**
   * @param string|null $text
   *
   * @return $this
   */
  public function setText(?string $text)
  {
    $this->text = $text;

    return $this;
  }
}
