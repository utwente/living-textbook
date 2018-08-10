<?php

namespace App\Form\Permission;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Form\Type\EmailListType;
use App\Form\Type\SaveType;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
    /** @var UserGroup $userGroup */
    $userGroup = $options['user_group'];

    $builder
        ->add('users', EntityType::class, [
            'required'      => false,
            'label'         => 'permissions.users',
            'class'         => User::class,
            'choice_label'  => 'selectionName',
            'query_builder' => function (UserRepository $userRepository) use ($userGroup) {
              return $userRepository->getAvailableUsersForUserGroupQueryBuilder($userGroup);
            },
            'select2'       => true,
            'multiple'      => true,
        ])
        ->add('emails', EmailListType::class, [
            'required'    => false,
            'label'       => 'permissions.emails',
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
            'enable_list'          => false,
            'enable_save_and_list' => false,
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('user_group')
        ->setAllowedTypes('user_group', UserGroup::class);
  }
}
