<?php

namespace App\Form\User;

use App\Entity\UserApiToken;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class GenerateApiTokenType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('description', TextareaType::class, [
            'required'    => false,
            'label'       => 'user.api-tokens.description',
            'constraints' => [
                new Length(['max' => 255]),
            ],
        ])
        ->add('validUntil', DateTimeType::class, [
            'label'    => 'user.api-tokens.valid-until',
            'required' => false,
            'input'    => 'datetime_immutable',
            'widget'   => 'single_text',
            'html5'    => true,
        ])
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'enable_save_and_list' => false,
            'save_label'           => 'user.api-tokens.generate',
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_user_apitokens',
            'cancel_route_params'  => [],
        ]);;
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', UserApiToken::class);
  }
}
