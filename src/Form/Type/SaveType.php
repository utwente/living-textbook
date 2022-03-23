<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SaveType.
 *
 * @author BobV
 */
class SaveType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    // Add the save button if required
    if ($options['enable_save']) {
      $builder->add('_save', SubmitType::class, [
          'label' => $options['save_label'],
          'icon'  => $options['save_icon'],
          'attr'  => [
              'class' => 'btn btn-outline-success',
          ],
      ]);
    }

    // Add the save and list button if required
    if ($options['enable_save_and_list']) {
      $builder->add('_save_and_list', SubmitType::class, [
          'label' => $options['save_and_list_label'],
          'icon'  => $options['save_and_list_icon'],
          'attr'  => [
              'class' => 'btn btn-outline-info',
          ],
      ]);
    }

    if ($options['enable_cancel']) {
      $builder->add('_cancel', ButtonUrlType::class, [
          'label'        => $options['cancel_label'],
          'route'        => $options['cancel_route'],
          'route_params' => $options['cancel_route_params'],
          'icon'         => $options['cancel_icon'],
          'attr'         => [
              'class' => $options['cancel_btn_class'],
          ],
      ]);
    }
  }

  /** Build view. */
  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['locate_static'] = $options['locate_static'];
  }

  /**
   * Check whether the "save and list" button is clicked.
   *
   * @return bool
   */
  public static function isListClicked(FormInterface $form)
  {
    assert($form instanceof Form);
    $clickedButton = $form->getClickedButton();
    if ($form->isSubmitted()
        && $clickedButton instanceof SubmitButton
        && $clickedButton->getName() === '_save_and_list'
    ) {
      return true;
    }

    return false;
  }

  /** @param OptionsResolver $resolver */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'mapped'               => false,
        'save_label'           => 'form.save',
        'save_icon'            => 'fa-check',
        'save_and_list_label'  => 'form.save-and-list',
        'save_and_list_icon'   => 'fa-check',
        'list_label'           => 'form.list',
        'list_icon'            => 'fa-list',
        'cancel_label'         => 'form.cancel',
        'cancel_icon'          => 'fa-times',
        'cancel_btn_class'     => 'btn btn-outline-danger',
        'enable_save'          => true,
        'enable_save_and_list' => true,
        'enable_cancel'        => false,
        'cancel_route'         => null,
        'cancel_route_params'  => [],
        'locate_static'        => false,
    ]);

    $resolver->setAllowedTypes('save_label', 'string');
    $resolver->setAllowedTypes('save_and_list_label', 'string');
    $resolver->setAllowedTypes('list_label', 'string');
    $resolver->setAllowedTypes('enable_save', 'bool');
    $resolver->setAllowedTypes('enable_save_and_list', 'bool');
    $resolver->setAllowedTypes('enable_cancel', 'bool');
    $resolver->setAllowedTypes('cancel_route', ['null', 'string']);
    $resolver->setAllowedTypes('cancel_route_params', 'array');
    $resolver->setAllowedTypes('locate_static', 'bool');

    $resolver->setNormalizer('cancel_route', function (Options $options, $value) {
      if ($options['enable_cancel'] === true && $value === null) {
        throw new MissingOptionsException('The option "cancel_route" is not set, while the cancel button is enabled.');
      }

      return $value;
    });
  }
}
