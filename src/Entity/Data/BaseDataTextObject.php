<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class BaseDataTextObject.
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
   * Learning outcomes.
   *
   * @var string|null
   *
   * @ORM\Column(name="text", type="text", nullable=true)
   *
   * @Serializer\Groups({"review_change"})
   * @Serializer\Type("string")
   */
  private $text;

  /** Determine whether this block has data. */
  public function hasData(): bool
  {
    return $this->text !== null && $this->text != '';
  }

  /** @return string|null */
  public function getText(): ?string
  {
    return $this->text;
  }

  /** @return $this */
  public function setText(?string $text)
  {
    $this->text = $text;

    return $this;
  }
}
