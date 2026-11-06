<?php

namespace App\Entity\Skills;

enum SkillSetEnum: int
{
  case REMEMBER   = 1;
  case UNDERSTAND = 2;
  case APPLY      = 3;
  case ANALYSE    = 4;
  case EVALUATE   = 5;
  case CREATE     = 6;
}
