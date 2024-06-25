<?php

namespace App\Form\Type;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonUrlType extends AbstractType
{
  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['route']        = $options['route'];
    $view->vars['route_params'] = $options['route_params'];
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setRequired('route');

    $resolver->setDefault('route_params', []);

    $resolver->setAllowedTypes('route', 'string');
    $resolver->setAllowedTypes('route_params', 'array');
  }

  #[Override]
  public function getParent(): ?string
  {
    return ButtonType::class;
  }
}
