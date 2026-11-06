<?php

namespace App\Entity\Skills;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Embeddable]
class UnderstandSkills extends AbstractSkills
{

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $classifying  = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $comparing    = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $exemplifying = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $explaining   = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $inferring    = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $interpreting = null;

  #[ORM\Column(nullable: true)]
  #[Assert\Length(max: 255)]
  public ?string $summarising  = null;

  public function getSkills(): array
  {
    return [
      $this->classifying,
      $this->comparing,
      $this->exemplifying,
      $this->explaining,
      $this->inferring,
      $this->interpreting,
      $this->summarising,
    ];
  }
}
