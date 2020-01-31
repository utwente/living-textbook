<?php

namespace App\Form\Permission;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionsTypes extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    foreach ($options['group_types'] as $groupType) {
      $builder->add($groupType, CheckboxType::class, [
          'label'      => 'permissions.type.' . $groupType,
          'help'       => 'permissions.type-help.' . $groupType,
          'required'   => false,
          'hide_label' => true,
      ]);
    }
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('group_types')
        ->setAllowedTypes('group_types', 'array');
  }
}
