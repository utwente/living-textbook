<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class OrderedCollectionElementType extends AbstractType
{
  /** Add the 'collection-position' class as last var in the view. */
  public function finishView(FormView $view, FormInterface $form, array $options)
  {
    if (!array_key_exists('attr', $view->vars)) {
      $view->vars['attr'] = [];
    }
    if (!array_key_exists('class', $view->vars['attr'])) {
      $view->vars['attr']['class'] = '';
    }

    /* @phan-suppress-next-line PhanTypeInvalidDimOffset, PhanTypeArraySuspiciousNullable */
    $view->vars['attr']['class'] .= ' collection-position';
  }

  /** @return string|null */
  public function getParent()
  {
    return HiddenType::class;
  }
}
