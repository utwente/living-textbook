<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class RemoveType.
 *
 * @author BobV
 */
class RemoveType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
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

  /**
   * Check whether the "remove" button is clicked.
   *
   * @return bool
   */
  public static function isRemoveClicked(FormInterface $form)
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

  /** @param OptionsResolver $resolver */
  public function configureOptions(OptionsResolver $resolver)
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
