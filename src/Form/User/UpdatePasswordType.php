<?php

namespace App\Form\User;

use App\Form\Type\NewPasswordType;
use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class UpdatePasswordType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('password', NewPasswordType::class)
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => 'app_user_fallbacklist',
        'cancel_route_params'  => [],
      ]);
  }
}
