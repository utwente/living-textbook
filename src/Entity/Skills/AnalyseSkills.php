<?php

namespace App\Entity\Skills;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class AnalyseSkills extends AbstractSkills
{
  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $attributing = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $differentiating = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $organising = null;

  public function getSkills(): array
  {
    return [
      $this->attributing,
      $this->differentiating,
      $this->organising,
    ];
  }
}
