<?php

namespace App\Form\Type;

use Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class NewPasswordType extends AbstractType
{
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'type'            => \Symfony\Component\Form\Extension\Core\Type\PasswordType::class,
        'constraints'     => [
          // Max length due to BCrypt, @see BCryptPasswordEncoder
            new Length(['max' => 72]),
            new PasswordStrength([
                'minLength'   => 8,
                'minStrength' => 4,
                'message'     => 'user.password-too-weak',
            ]),
        ],
        'invalid_message' => 'user.password-no-match',
        'first_options'   => array('label' => 'user.password'),
        'second_options'  => array('label' => 'user.repeat-password'),
    ]);
  }

  public function getParent()
  {
    return RepeatedType::class;
  }
}
