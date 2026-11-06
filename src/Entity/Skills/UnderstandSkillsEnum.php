<?php

namespace App\Entity\Skills;

enum UnderstandSkillsEnum: int implements SkillsEnumInterface
{
  case CLASSIFYING  = 1;
  case COMPARING    = 2;
  case EXEMPLIFYING = 3;
  case EXPLAINING   = 4;
  case INFERRING    = 5;
  case INTERPRETING = 6;
  case SUMMARISING  = 7;

  public function getSkillSet(): SkillSetEnum
  {
    return SkillSetEnum::UNDERSTAND;
  }
}
