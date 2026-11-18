<?php

namespace App\Entity\Skills;

use BackedEnum;
use Symfony\Component\Translation\TranslatableMessage;

interface SkillsEnumInterface extends BackedEnum
{
  public function getSkillSet(): SkillSetEnum;

  public function label(): TranslatableMessage;
}
