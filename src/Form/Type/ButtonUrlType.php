<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ButtonUrlType.
 *
 * @author BobV
 */
class ButtonUrlType extends AbstractType
{
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['route']        = $options['route'];
    $view->vars['route_params'] = $options['route_params'];
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('route');

    $resolver->setDefault('route_params', []);

    $resolver->setAllowedTypes('route', 'string');
    $resolver->setAllowedTypes('route_params', 'array');
  }

  public function getParent()
  {
    return ButtonType::class;
  }
}
