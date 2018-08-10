<?php

namespace App\Form\User;

use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class ChangePasswordType
 */
class ChangePasswordType extends AbstractType
{
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
        ->add('password', RepeatedType::class, [
            'type'            => PasswordType::class,
            'constraints'     => [
                // Max length due to BCrypt, @see BCryptPasswordEncoder
                new Length(['min' => 8, 'max' => 72]),
            ],
            'invalid_message' => 'user.password-no-match',
            'first_options'   => array('label' => 'user.password'),
            'second_options'  => array('label' => 'user.repeat-password'),
        ])
        ->add('submit', SaveType::class, [
            'locate_static'        => false,
            'enable_save_and_list' => false,
            'enable_cancel'        => true,
            'enable_list'          => false,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_user_fallbacklist',
            'cancel_route_params'  => [],
        ]);
  }
}
