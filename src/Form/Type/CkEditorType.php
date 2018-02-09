<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Class CkEditorType
 * Extended CkEditorType to control default configuration
 *
 * @author BobV
 */
class CkEditorType extends AbstractType
{
  public function getParent()
  {
    return \Ivory\CKEditorBundle\Form\Type\CKEditorType::class;
  }
}
