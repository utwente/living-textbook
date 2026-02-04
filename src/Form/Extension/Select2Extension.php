<?php

namespace App\Form\Extension;

use Override;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class provides a Select2 form extension, to be able to easily set
 * select2 options on the form.
 */
class Select2Extension extends AbstractTypeExtension
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder->setAttribute('select2', $options['select2']);
    $builder->setAttribute('select2_allow_clear', $options['select2_allow_clear']);
    $builder->setAttribute('select2_placeholder', $options['select2_placeholder']);
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['select2']             = $options['select2'];
    $view->vars['select2_allow_clear'] = $options['select2_allow_clear'];
    $view->vars['select2_placeholder'] = $options['select2_placeholder'];
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'select2'             => false,
      'select2_allow_clear' => null,
      'select2_placeholder' => null,
    ]);

    $resolver->setAllowedTypes('select2', ['bool']);
    $resolver->setAllowedTypes('select2_allow_clear', ['null', 'bool']);
    $resolver->setAllowedTypes('select2_placeholder', ['null', 'string']);

    $resolver->setNormalizer('select2_allow_clear', static function (Options $options, $value) {
      if ($value !== null) {
        return $value;
      }

      if (!$options->offsetExists('required')) {
        return $value;
      }

      return !$options->offsetGet('required');
    });
  }

  #[Override]
  public static function getExtendedTypes(): iterable
  {
    return [ChoiceType::class];
  }
}
