<?php

namespace App\Form\Type;

use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;

class HiddenEntityType extends AbstractType
{
  #[Override]
  public function getParent()
  {
    return EntityType::class;
  }
}
