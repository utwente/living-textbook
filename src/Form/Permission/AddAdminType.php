<?php

namespace App\Form\Permission;

use App\Entity\User;
use App\Form\Type\SaveType;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddAdminType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('admin', EntityType::class, [
            'label'         => 'permissions.admin',
            'class'         => User::class,
            'choice_label'  => 'selectionName',
            'query_builder' => function (UserRepository $ur) {
              return $ur->createQueryBuilder('u')
                  ->where('u.isAdmin = false')
                  ->orderBy('u.displayName', 'ASC');
            },
            'select2'       => true,
        ])
        ->add('submit', SaveType::class, [
            'list_route'           => 'app_concept_list',
            'enable_save_and_list' => false,
            'enable_cancel'        => true,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_permissions_admins',
        ]);
  }
}
