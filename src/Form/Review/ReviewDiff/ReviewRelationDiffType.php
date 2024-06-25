<?php

namespace App\Form\Review\ReviewDiff;

use Override;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ReviewRelationDiffType extends AbstractReviewDiffType
{
  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    parent::buildView($view, $form, $options);
    $view->vars['incoming'] = $options['incoming'];

    $propertyAccessor            = PropertyAccess::createPropertyAccessor();
    $pendingChange               = $this->getPendingChange($options);
    $view->vars['concept']       = $pendingChange->getObject();
    $view->vars['new_relations'] = $propertyAccessor->getValue($this->getPendingChange($options)->getObject(), $options['field']);

    if (null !== $options['original_object']) {
      $view->vars['orig_relations'] = $propertyAccessor->getValue($options['original_object'], $options['field']);
    }
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    parent::configureOptions($resolver);

    $resolver
      ->setRequired('incoming')
      ->setAllowedTypes('incoming', 'bool');
  }
}
