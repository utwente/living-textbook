<?php

namespace App\Form\Permission;

use App\Entity\UserGroup;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionsTypes extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    foreach ($options['group_types'] as $groupType) {
      $groupOptions = [
        'label'      => 'permissions.type.' . $groupType,
        'help'       => 'permissions.type-help.' . $groupType,
        'required'   => false,
        'hide_label' => true,
      ];

      // Disable the viewer input, as it must always be applied
      if ($groupType === UserGroup::GROUP_VIEWER) {
        $groupOptions = array_merge($groupOptions, [
          'disabled' => true,
          'data'     => true,
        ]);
      }

      $builder
        ->add($groupType, CheckboxType::class, $groupOptions);
    }

    // Add transformer to enforce viewer role set to true
    $builder->addModelTransformer(new CallbackTransformer(
      function () {
        // No-op
      },
      function ($data) {
        $data[UserGroup::GROUP_VIEWER] = true;

        return $data;
      }
    ));
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setRequired('group_types')
      ->setAllowedTypes('group_types', 'array');
  }
}
