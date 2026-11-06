<?php

namespace App\Entity\Skills;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class EvaluateSkills extends AbstractSkills
{
  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $checking = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $critiquing = null;

  public function getSkills(): array
  {
    return [
      $this->checking,
      $this->critiquing,
    ];
  }
}
