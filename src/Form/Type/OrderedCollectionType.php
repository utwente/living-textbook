<?php

namespace App\Form\Type;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderedCollectionType extends AbstractType
{
  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['position_selector'] = $options['position_selector'];
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefault('position_selector', 'collection-position');
  }

  #[Override]
  public function getParent(): ?string
  {
    return CollectionType::class;
  }
}
