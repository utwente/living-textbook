<?php

namespace App\Form\Type;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ButtonTypeInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class SingleSubmitType extends AbstractType implements ButtonTypeInterface
{
  #[Override]
  public function getParent(): ?string
  {
    return ButtonType::class;
  }
}
