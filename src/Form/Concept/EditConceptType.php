<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use App\Entity\Data\DataIntroduction;
use App\Entity\Data\DataLearningOutcomes;
use App\Entity\Data\DataTheoryExplanation;
use App\Form\Data\BaseDataTextType;
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
        ->add('introduction', BaseDataTextType::class, [
            'label'      => 'concept.introduction',
            'required'   => true,
            'data_class' => DataIntroduction::class,
        ])
        ->add('learningOutcomes', BaseDataTextType::class, [
            'label'      => 'concept.learning-outcomes',
            'required'   => false,
            'data_class' => DataLearningOutcomes::class,
        ])
        ->add('theoryExplanation', BaseDataTextType::class, [
            'label'      => 'concept.theory-explanation',
            'required'   => false,
            'data_class' => DataTheoryExplanation::class,
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
