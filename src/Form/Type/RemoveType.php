<?php

namespace App\Form\Type;

use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function assert;

class RemoveType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('_remove', SubmitType::class, [
        'label' => $options['remove_label'],
        'icon'  => 'fa-check',
        'attr'  => [
          'class' => 'btn btn-' . $options['remove_btn_variant'],
        ],
      ])
      ->add('_cancel', ButtonUrlType::class, [
        'label'        => $options['cancel_label'],
        'icon'         => 'fa-times',
        'route'        => $options['cancel_route'],
        'route_params' => $options['cancel_route_params'],
      ]);
  }

  /** Check whether the "remove" button is clicked. */
  public static function isRemoveClicked(FormInterface $form): bool
  {
    assert($form instanceof Form);
    $clickedButton = $form->getClickedButton();

    if ($form->isSubmitted()
        && $clickedButton instanceof SubmitButton
        && $clickedButton->getName() === '_remove'
    ) {
      return true;
    }

    return false;
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'mapped'              => false,
      'remove_btn_variant'  => 'outline-danger',
      'remove_label'        => 'form.confirm-remove',
      'cancel_label'        => 'form.cancel',
      'cancel_route_params' => [],
    ]);

    $resolver->setRequired('cancel_route');

    $resolver->setAllowedTypes('remove_btn_variant', 'string');
    $resolver->setAllowedTypes('remove_label', 'string');
    $resolver->setAllowedTypes('cancel_label', 'string');
    $resolver->setAllowedTypes('cancel_route', 'string');
    $resolver->setAllowedTypes('cancel_route_params', 'array');
  }
}
