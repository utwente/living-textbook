<?php

namespace App\Form\StudyArea;

use App\Entity\StudyAreaFieldConfiguration;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldConfigurationType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $this
        ->conceptFields($builder)
        ->learningOutcomeFields($builder);

    $builder
        ->add('submit', SaveType::class, [
            'enable_save_and_list' => false,
            'enable_cancel'        => false,
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
        'data_class' => StudyAreaFieldConfiguration::class,
    ]);
  }

  private function conceptFields(FormBuilderInterface $builder): self
  {
    $builder
        ->add('concept_definition_name', NULL, [
            'form_header' => 'concept._name',
            'label'       => 'concept.definition',
            'attr'        => [
                'placeholder' => 'concept.definition',
            ],
        ])
        ->add('concept_introduction_name', NULL, [
            'label' => 'concept.introduction',
            'attr'  => [
                'placeholder' => 'concept.introduction',
            ],
        ])
        ->add('concept_synonyms_name', NULL, [
            'label' => 'concept.synonyms',
            'attr'  => [
                'placeholder' => 'concept.synonyms',
            ],
        ])
        ->add('concept_prior_knowledge_name', NULL, [
            'label' => 'concept.prior-knowledge',
            'attr'  => [
                'placeholder' => 'concept.prior-knowledge',
            ],
        ])
        ->add('concept_theory_explanation_name', NULL, [
            'label' => 'concept.theory-explanation',
            'attr'  => [
                'placeholder' => 'concept.theory-explanation',
            ],
        ])
        ->add('concept_how_to_name', NULL, [
            'label' => 'concept.how-to',
            'attr'  => [
                'placeholder' => 'concept.how-to',
            ],
        ])
        ->add('concept_examples_name', NULL, [
            'label' => 'concept.examples',
            'attr'  => [
                'placeholder' => 'concept.examples',
            ],
        ])
        ->add('concept_self_assessment_name', NULL, [
            'label' => 'concept.self-assessment',
            'attr'  => [
                'placeholder' => 'concept.self-assessment',
            ],
        ]);

    return $this;
  }

  private function learningOutcomeFields(FormBuilderInterface $builder): self
  {
    $builder
        ->add('learningOutcomeObjName', NULL, [
            'form_header' => 'learning-outcome._name',
            'label'       => 'field-configuration.obj-name',
            'help'        => 'field-configuration.obj-help',
            'attr'        => [
                'placeholder' => 'learning-outcome._name',
            ],
        ]);

    return $this;
  }
}
