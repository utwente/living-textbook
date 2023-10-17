<?php

namespace App\Form\Permission;

use App\Form\Type\EmailListType;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPermissionsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('permissions', PermissionsTypes::class, [
        'group_types' => $options['group_types'],
        'label'       => 'permissions.permission',
      ])
      ->add('emails', EmailListType::class)
      ->add('submit', SaveType::class, [
        'enable_cancel'        => true,
        'cancel_route'         => 'app_permissions_studyarea',
        'enable_save_and_list' => false,
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setRequired('group_types')
      ->setAllowedTypes('group_types', 'array');
  }
}
