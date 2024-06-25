<?php

namespace App\Form\Extension;

use Override;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonExtension extends AbstractTypeExtension
{
  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['icon'] = $options['icon'] === null ? null
        : sprintf('%s %s', $options['icon_prefix'], $options['icon']);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'icon'        => null,
      'icon_prefix' => 'fa fa-fw',
    ]);

    $resolver->setAllowedTypes('icon', ['string', 'null']);
    $resolver->setAllowedTypes('icon_prefix', ['string']);
  }

  #[Override]
  public static function getExtendedTypes(): iterable
  {
    return [ButtonType::class];
  }
}
