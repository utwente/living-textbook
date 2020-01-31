<?php

namespace App\Form\Permission;

use App\Entity\StudyArea;
use App\Entity\UserGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PermissionsTypes extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var StudyArea $studyArea */
    $studyArea = $options['study_area'];

    foreach (UserGroup::getGroupTypes() as $groupType) {
      if ($studyArea->getAccessType() === StudyArea::ACCESS_PUBLIC && $groupType === UserGroup::GROUP_VIEWER) {
        continue;
      }

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
        ->setRequired('study_area')
        ->setAllowedTypes('study_area', StudyArea::class);
  }
}
