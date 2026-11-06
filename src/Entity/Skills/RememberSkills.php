<?php

namespace App\Entity\Skills;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class RememberSkills extends AbstractSkills
{
  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $recalling = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $recognising = null;

  public function getSkills(): array
  {
    return [
      $this->recalling,
      $this->recognising,
    ];
  }
}
