<?php

namespace App\Form\Authentication;

use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('_username', TextType::class, [
        'label' => 'login.username',
      ])
      ->add('_password', PasswordType::class, [
        'label' => 'login.password',
        'attr'  => [
          'autocomplete' => false,
        ],
      ])
      ->add('submit', SaveType::class, [
        'save_label'           => 'login.login',
        'cancel_label'         => 'auth.forgot-password',
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_route'         => 'app_authentication_resetpassword',
        'cancel_icon'          => 'fa-question',
        'cancel_btn_class'     => 'btn btn-outline-secondary',
      ]);
  }
}
