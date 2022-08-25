<?php

namespace App\Form\User;

use App\Entity\User;
use App\Form\Type\NewPasswordType;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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
        ->add('password', NewPasswordType::class)
        ->add('submit', SaveType::class, [
            'save_label'           => 'auth.create-account',
            'enable_save_and_list' => false,
            'enable_cancel'        => false,
        ]);

    // Transformer to set displayname same as fullname
    $builder->addModelTransformer(new CallbackTransformer(fn (User $user) => $user, fn (User $user) => $user->setDisplayName($user->getFullName())));
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefault('data_class', User::class);
  }
}
