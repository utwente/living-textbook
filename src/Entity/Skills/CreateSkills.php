<?php

namespace App\Entity\Skills;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class CreateSkills extends AbstractSkills
{
  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $generating = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $planning = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $producing = null;

  public function getSkills(): array
  {
    return [
      $this->generating,
      $this->planning,
      $this->producing,
    ];
  }
}
