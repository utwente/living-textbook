<?php

namespace App\Form\Review\ReviewDiff;

use Override;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ReviewCheckboxDiffType extends AbstractReviewDiffType
{
  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    parent::buildView($view, $form, $options);

    $propertyAccessor        = PropertyAccess::createPropertyAccessor();
    $view->vars['new_value'] = $propertyAccessor->getValue($this->getPendingChange($options)->getObject(), $options['field']);

    if (null !== $options['original_object']) {
      $view->vars['orig_value'] = $propertyAccessor->getValue($options['original_object'], $options['field']);
    }
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);

    $resolver
      ->setAllowedValues('field', ['instance']);
  }
}
