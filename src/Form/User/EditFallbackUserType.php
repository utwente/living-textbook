<?php

namespace App\Form\User;

use App\Entity\User;
use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditFallbackUserType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
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
      ->add('submit', SaveType::class, [
        'enable_cancel'       => true,
        'cancel_label'        => 'form.discard',
        'cancel_route'        => 'app_user_fallbacklist',
        'cancel_route_params' => [],
      ]);

    // Transformer to set displayname same as fullname
    $builder->addModelTransformer(new CallbackTransformer(fn (User $user) => $user, fn (User $user) => $user->setDisplayName($user->getFullName())));
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefault('data_class', User::class);
  }
}
