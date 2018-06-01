<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class SingleSubmitType extends AbstractType
{
  public function getParent()
  {
    return ButtonType::class;
  }

}
