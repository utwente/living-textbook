<?php

namespace App\Form\Permission;

use App\Entity\User;
use App\Form\Type\SaveType;
use App\Repository\UserRepository;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class AddAdminType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('admin', EntityType::class, [
        'label'         => 'permissions.admin',
        'class'         => User::class,
        'choice_label'  => 'selectionName',
        'query_builder' => static fn (UserRepository $ur) => $ur->createQueryBuilder('u')
          ->where('u.isAdmin = false')
          ->orderBy('u.displayName', 'ASC'),
        'select2' => true,
      ])
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => 'app_permissions_admins',
      ]);
  }
}
