<?php

namespace App\Form\Authentication;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {

    $builder
        ->add('_username', TextType::class, array(
            'label' => 'login.username',
        ))
        ->add('_password', PasswordType::class, array(
            'label' => 'login.password',
            'attr' => [
                'autocomplete' => false,
            ]
        ))
        ->add('submit', SubmitType::class, array(
            'label' => 'login.login',
            'attr' => [
                'class' => 'btn-outline-primary',
            ],
        ));
  }
}
