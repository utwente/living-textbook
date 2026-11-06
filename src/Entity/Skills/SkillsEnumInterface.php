<?php

namespace App\Entity\Skills;

use BackedEnum;

interface SkillsEnumInterface extends BackedEnum
{
  public function getSkillSet(): SkillSetEnum;
}
