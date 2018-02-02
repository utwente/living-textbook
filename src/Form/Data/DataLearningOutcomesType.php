<?php

namespace App\Form\Data;

use App\Entity\Data\DataLearningOutcomes;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DataLearningOutcomesType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('learningOutcomes', TextareaType::class, [
            'label'    => 'concept.learning-outcomes',
            'required' => false,
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'data_class' => DataLearningOutcomes::class,
    ]);
  }

}
