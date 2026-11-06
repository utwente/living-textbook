<?php

namespace App\Entity\Data;

use App\Database\Traits\Blameable;
use App\Database\Traits\IdTrait;
use App\Database\Traits\SoftDeletable;
use App\Entity\Skills\AnalyseSkills;
use App\Entity\Skills\ApplySkills;
use App\Entity\Skills\CreateSkills;
use App\Entity\Skills\EvaluateSkills;
use App\Entity\Skills\RememberSkills;
use App\Entity\Skills\SkillsInterface;
use App\Entity\Skills\UnderstandSkills;
use App\Repository\Data\DataSkillsRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/** Skills are the new learning outcomes, which follow a predefined pattern. */
#[ORM\Entity(repositoryClass: DataSkillsRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
class DataSkills implements DataInterface
{
  use Blameable;
  use IdTrait;
  use SoftDeletable;

  #[ORM\Embedded]
  private AnalyseSkills $analyse;

  #[ORM\Embedded]
  private ApplySkills $apply;

  #[ORM\Embedded]
  private CreateSkills $create;

  #[ORM\Embedded]
  private EvaluateSkills $evaluate;

  #[ORM\Embedded]
  private RememberSkills $remember;

  #[ORM\Embedded]
  private UnderstandSkills $understand;

  public function __construct()
  {
    $this->analyse    = new AnalyseSkills();
    $this->apply      = new ApplySkills();
    $this->create     = new CreateSkills();
    $this->evaluate   = new EvaluateSkills();
    $this->remember   = new RememberSkills();
    $this->understand = new UnderstandSkills();
  }

  public function hasData(): bool
  {
    return array_any(
      [
        $this->analyse,
        $this->apply,
        $this->create,
        $this->evaluate,
        $this->remember,
        $this->understand,
      ],
      static fn (SkillsInterface $skillSet) => $skillSet->hasData(),
    );
  }
}
