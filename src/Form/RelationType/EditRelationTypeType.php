<?php

namespace App\Form\RelationType;

use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class EditRelationTypeType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', TextType::class, [
            'label' => 'relation-type.name',
        ])
        ->add('description', TextareaType::class, [
            'label' => 'relation-type.description',
        ])
        ->add('submit', SaveType::class, [
            'enable_save_and_list' => false,
            'enable_cancel'        => true,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_relationtype_list',
        ]);
  }
}
