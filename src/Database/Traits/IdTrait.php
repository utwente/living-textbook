<?php

namespace App\Database\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IdTrait
{
  /**
   * @JMS\Serializer\Annotation\Expose()
   *
   * @JMS\Serializer\Annotation\Groups({"Default", "review_change", "id_only"})
   *
   * @JMS\Serializer\Annotation\Type("int")
   */
  #[ORM\Column]
  #[ORM\Id]
  #[ORM\GeneratedValue]
  private ?int $id = null;

  public function getId(): ?int
  {
    return $this->id;
  }
}
