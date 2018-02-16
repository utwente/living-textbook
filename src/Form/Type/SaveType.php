<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SaveType
 *
 * @author BobV
 */
class SaveType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    // Add the save button if required
    if ($options['enable_save']) {
      $builder->add('_save', SubmitType::class, array(
          'label' => $options['save_label'],
          'icon'  => 'fa-check',
          'attr'  => array(
              'class' => 'btn btn-outline-success',
          ),
      ));
    }

    // Add the save and list button if required
    if ($options['enable_save_and_list']) {
      $builder->add('_save_and_list', SubmitType::class, array(
          'label' => $options['save_and_list_label'],
          'icon'  => 'fa-check',
          'attr'  => array(
              'class' => 'btn btn-outline-info',
          ),
      ));
    }

    if ($options['enable_cancel']) {
      $builder->add('_cancel', ButtonUrlType::class, array(
          'label'        => $options['cancel_label'],
          'route'        => $options['cancel_route'],
          'route_params' => $options['cancel_route_params'],
          'icon'         => 'fa-times',
          'attr'  => array(
              'class' => 'btn btn-outline-danger',
          ),
      ));
    }

    // Add the list button if required
    if ($options['enable_list']) {
      $builder->add('_list', ButtonUrlType::class, array(
          'label'        => $options['list_label'],
          'route'        => $options['list_route'],
          'route_params' => $options['list_route_params'],
          'icon'         => 'fa-list',
      ));
    }
  }

  /**
   * Check whether the "save and list" button is clicked
   *
   * @param FormInterface $form
   *
   * @return bool
   */
  public static function isListClicked(FormInterface $form)
  {
    assert($form instanceof Form);
    if ($form->isSubmitted()
        && $form->getClickedButton()
        && $form->getClickedButton()->getName() === '_save_and_list'
    ) {
      return true;
    }

    return false;
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {

    $resolver->setDefaults(array(
        'mapped'               => false,
        'save_label'           => 'form.save',
        'save_and_list_label'  => 'form.save-and-list',
        'list_label'           => 'form.list',
        'cancel_label'         => 'form.cancel',
        'enable_save'          => true,
        'enable_save_and_list' => true,
        'enable_list'          => true,
        'enable_cancel'        => false,
        'list_route'           => NULL,
        'list_route_params'    => array(),
        'cancel_route'         => NULL,
        'cancel_route_params'  => array(),
    ));

    $resolver->setAllowedTypes('save_label', 'string');
    $resolver->setAllowedTypes('save_and_list_label', 'string');
    $resolver->setAllowedTypes('list_label', 'string');
    $resolver->setAllowedTypes('enable_save', 'bool');
    $resolver->setAllowedTypes('enable_save_and_list', 'bool');
    $resolver->setAllowedTypes('enable_list', 'bool');
    $resolver->setAllowedTypes('enable_cancel', 'bool');
    $resolver->setAllowedTypes('list_route', array('null', 'string'));
    $resolver->setAllowedTypes('list_route_params', 'array');
    $resolver->setAllowedTypes('cancel_route', array('null', 'string'));
    $resolver->setAllowedTypes('cancel_route_params', 'array');

    $resolver->setNormalizer('list_route', function (Options $options, $value) {
      if ($options['enable_list'] === true && $value === NULL) {
        throw new MissingOptionsException('The option "list_route" is not set, while the list button is enabled.');
      }

      return $value;
    });

    $resolver->setNormalizer('cancel_route', function (Options $options, $value) {
      if ($options['enable_cancel'] === true && $value === NULL) {
        throw new MissingOptionsException('The option "cancel_route" is not set, while the cancel button is enabled.');
      }

      return $value;
    });
  }

}
