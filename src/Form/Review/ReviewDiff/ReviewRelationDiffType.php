<?php

namespace App\Form\Review\ReviewDiff;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ReviewRelationDiffType extends AbstractReviewDiffType
{
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    parent::buildView($view, $form, $options);
    $view->vars['incoming'] = $options['incoming'];

    $propertyAccessor             = PropertyAccess::createPropertyAccessor();
    $pendingChange                = $this->getPendingChange($options);
    $view->vars['concept']        = $pendingChange->getObject();
    $view->vars['orig_relations'] = $propertyAccessor->getValue($options['original_object'], $options['field']);
    $view->vars['new_relations']  = $propertyAccessor->getValue($this->getPendingChange($options)->getObject(), $options['field']);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    parent::configureOptions($resolver);

    $resolver
        ->setRequired('incoming')
        ->setAllowedTypes('incoming', 'bool');
  }


}
