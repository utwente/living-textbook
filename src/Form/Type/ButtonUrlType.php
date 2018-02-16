<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ButtonUrlType
 *
 * @author BobV
 */
class ButtonUrlType extends AbstractType
{

  /**
   * @param FormView      $view
   * @param FormInterface $form
   * @param array         $options
   */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['route']        = $options['route'];
    $view->vars['route_params'] = $options['route_params'];
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('route');

    $resolver->setDefault('route_params', array());

    $resolver->setAllowedTypes('route', 'string');
    $resolver->setAllowedTypes('route_params', 'array');
  }

  /**
   * @return mixed
   */
  public function getParent()
  {
    return ButtonType::class;
  }

}
