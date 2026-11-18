<?php

namespace App\Entity\Skills;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class ApplySkills extends AbstractSkills
{
  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $executing    = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $implementing = null;

  public function getSkills(): array
  {
    return [
      $this->executing,
      $this->implementing,
    ];
  }
}
