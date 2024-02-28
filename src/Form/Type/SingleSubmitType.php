<?php

namespace App\Form\Type;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class SingleSubmitType extends AbstractType
{
  #[Override]
  public function getParent()
  {
    return ButtonType::class;
  }
}
