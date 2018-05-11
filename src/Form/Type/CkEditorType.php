<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;

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
    return \FOS\CKEditorBundle\Form\Type\CKEditorType::class;
  }
}
