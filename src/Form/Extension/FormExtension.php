<?php

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class provides our extra form extension, such as:
 *  - hide_label
 *  - form_header
 *
 * Class FormExtension
 *
 * @author BobV
 */
class FormExtension extends AbstractTypeExtension
{

  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->setAttribute('hide_label', $options['hide_label']);
    $builder->setAttribute('full_width_label', $options['full_width_label']);
    $builder->setAttribute('form_header', $options['form_header']);
  }

  /**
   * @param FormView      $view
   * @param FormInterface $form
   * @param array         $options
   */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['hide_label']       = $options['hide_label'];
    $view->vars['full_width_label'] = $options['full_width_label'];
    $view->vars['form_header']      = $options['form_header'];
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'hide_label'       => false,
        'full_width_label' => false,
        'form_header'      => NULL,
    ]);
    $resolver->setAllowedTypes('hide_label', ['bool']);
    $resolver->setAllowedTypes('full_width_label', ['bool']);
    $resolver->setAllowedTypes('form_header', ['null', 'string']);
  }

  public static function getExtendedTypes(): iterable
  {
    return [FormType::class];
  }
}
