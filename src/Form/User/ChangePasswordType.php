<?php

namespace App\Form\User;

use App\Form\Type\NewPasswordType;
use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

/**
 * Class ChangePasswordType.
 */
class ChangePasswordType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add('current', PasswordType::class, [
        'label'       => 'user.fallback.current-password',
        'constraints' => [
          new UserPassword([
            'message' => 'user.wrong-current-password',
          ]),
        ],
      ])
      ->add('password', NewPasswordType::class)
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => true,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => 'app_default_dashboard',
        'cancel_route_params'  => [],
      ]);
  }
}
