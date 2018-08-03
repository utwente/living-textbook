<?php

namespace App\Form\User;

use App\Entity\User;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddFallbackUserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('username', EmailType::class, [
            'label' => 'user.emailaddress',
        ])
        ->add('givenName', TextType::class, [
            'label' => 'user.given-name',
        ])
        ->add('familyName', TextType::class, [
            'label' => 'user.family-name',
        ])
        ->add('fullName', TextType::class, [
            'label' => 'user.full-name',
        ])
        ->add('password', RepeatedType::class, [
            'type'            => PasswordType::class,
            'invalid_message' => 'user.password-no-match',
            'first_options'   => array('label' => 'user.password'),
            'second_options'  => array('label' => 'user.repeat-password'),
        ])
        ->add('submit', SaveType::class, [
            'locate_static'       => false,
            'list_route'          => 'app_user_fallbacklist',
            'enable_cancel'       => true,
            'cancel_label'        => 'form.discard',
            'cancel_route'        => 'app_user_fallbacklist',
            'cancel_route_params' => [],
        ]);

    // Transformer to set displayname same as fullname
    $builder->addModelTransformer(new CallbackTransformer(function (User $user) {
      return $user;
    }, function (User $user) {
      return $user->setDisplayName($user->getFullName());
    }));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefault('data_class', User::class);
  }


}
