<?php

namespace App\Entity\Skills;

use Symfony\Component\Translation\TranslatableMessage;

use function strtolower;

trait SkillTranslationTrait
{
  public function label(): TranslatableMessage
  {
    return new TranslatableMessage('skill._set._' . strtolower($this->getSkillSet()->name) . '.' . strtolower($this->name));
  }
}
