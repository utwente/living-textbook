<?php

namespace App\Form\Permission;

use App\Entity\StudyArea;
use App\Form\Type\EmailListType;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class AddPermissionsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('permissions', PermissionsTypes::class, [
            'study_area' => $options['study_area'],
            'label'      => 'permissions.permission',
        ])
        ->add('emails', EmailListType::class, [
            'required'    => false,
            'label'       => 'permissions.emails',
            'help'        => 'permissions.emails-help',
            'constraints' => [
                'constraints' => new All([
                    new NotBlank(),
                    new Email(),
                ]),
            ],
        ])
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'cancel_route'         => 'app_permissions_studyarea',
            'enable_save_and_list' => false,
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('study_area')
        ->setAllowedTypes('study_area', StudyArea::class);
  }
}
