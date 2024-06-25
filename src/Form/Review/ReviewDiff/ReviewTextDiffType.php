<?php

namespace App\Form\Review\ReviewDiff;

use Override;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ReviewTextDiffType extends AbstractReviewDiffType
{
  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    parent::buildView($view, $form, $options);
    $view->vars['ckeditor'] = $options['ckeditor'];

    $propertyAccessor       = PropertyAccess::createPropertyAccessor();
    $view->vars['new_text'] = $propertyAccessor->getValue($this->getPendingChange($options)->getObject(), $options['field']);
    if ($options['has_data_object']) {
      $view->vars['new_text'] = $propertyAccessor->getValue($view->vars['new_text'], 'text');
    }

    if (null !== $options['original_object']) {
      $view->vars['orig_text'] = $propertyAccessor->getValue($options['original_object'], $options['field']);
      if ($options['has_data_object']) {
        $view->vars['orig_text'] = $propertyAccessor->getValue($view->vars['orig_text'], 'text');
      }
    }
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    parent::configureOptions($resolver);

    $resolver
      ->setDefault('has_data_object', false)
      ->setDefault('ckeditor', false)
      ->setAllowedTypes('has_data_object', 'bool')
      ->setAllowedTypes('ckeditor', 'bool');
  }
}
