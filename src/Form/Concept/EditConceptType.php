<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use App\Form\Data\DataIntroductionType;
use App\Form\Data\DataLearningOutcomesType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditConceptType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', TextType::class, [
            'label' => 'concept.name',
        ])
        ->add('introduction', DataIntroductionType::class, [
            'hide_label' => true,
        ])
        ->add('learningOutcomes', DataLearningOutcomesType::class, [
            'hide_label' => true,
        ])
        ->add('submit', SubmitType::class, [
            'label' => 'form.save',
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'data_class' => Concept::class,
    ]);
  }
}
