<?php

namespace App\Form\RelationType;

use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRelationTypeType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', TextType::class, [
            'label'    => 'relation-type.name',
            'disabled' => in_array('name', $options['disabled_fields']),
        ])
        ->add('description', TextareaType::class, [
            'label'    => 'relation-type.description',
            'required' => false,
            'disabled' => in_array('description', $options['disabled_fields']),
        ])
        ->add('submit', SaveType::class, [
            'enable_save_and_list' => false,
            'enable_cancel'        => true,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_relationtype_list',
        ]);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefault('disabled_fields', [])
        ->setAllowedTypes('disabled_fields', 'string[]');
  }
}
