<?php

namespace App\Form\Permission;

use App\Form\Type\EmailListType;
use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddPermissionsType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
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

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setRequired('group_types')
      ->setAllowedTypes('group_types', 'array');
  }
}
