<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderedCollectionType extends AbstractType
{
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['position_selector'] = $options['position_selector'];
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefault('position_selector', 'collection-position');
  }

  /** @return string|null */
  public function getParent()
  {
    return CollectionType::class;
  }
}
