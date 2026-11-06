<?php

namespace App\Form\StudyArea;

use App\Entity\StudyAreaFieldConfiguration;
use App\Form\Type\SaveType;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/** @extends AbstractType<StudyAreaFieldConfiguration> */
class FieldConfigurationType extends AbstractType
{
  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $this
      ->conceptFields($builder)
      ->skillFields($builder, $options);

    $builder
      ->add('submit', SaveType::class, [
        'enable_save_and_list' => false,
        'enable_cancel'        => false,
      ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setDefaults([
        'deprecated_skills' => false,
        'data_class'        => StudyAreaFieldConfiguration::class,
      ])
      ->setAllowedTypes('deprecated_skills', 'bool');
  }

  /** @param FormBuilderInterface<StudyAreaFieldConfiguration|null> $builder */
  private function conceptFields(FormBuilderInterface $builder): self
  {
    $builder
      ->add('conceptDefinitionName', null, [
        'form_header' => 'concept._name',
        'label'       => 'concept.definition',
        'attr'        => [
          'placeholder' => 'concept.definition',
        ],
      ])
      ->add('conceptIntroductionName', null, [
        'label' => 'concept.introduction',
        'attr'  => [
          'placeholder' => 'concept.introduction',
        ],
      ])
      ->add('conceptSynonymsName', null, [
        'label' => 'concept.synonyms',
        'attr'  => [
          'placeholder' => 'concept.synonyms',
        ],
      ])
      ->add('conceptPriorKnowledgeName', null, [
        'label' => 'concept.prior-knowledge',
        'attr'  => [
          'placeholder' => 'concept.prior-knowledge',
        ],
      ])
      ->add('conceptTheoryExplanationName', null, [
        'label' => 'concept.theory-explanation',
        'attr'  => [
          'placeholder' => 'concept.theory-explanation',
        ],
      ])
      ->add('conceptHowtoName', null, [
        'label' => 'concept.how-to',
        'attr'  => [
          'placeholder' => 'concept.how-to',
        ],
      ])
      ->add('conceptExamplesName', null, [
        'label' => 'concept.examples',
        'attr'  => [
          'placeholder' => 'concept.examples',
        ],
      ])
      ->add('conceptSelfAssessmentName', null, [
        'label' => 'concept.self-assessment',
        'attr'  => [
          'placeholder' => 'concept.self-assessment',
        ],
      ]);

    return $this;
  }

  /**
   * @param FormBuilderInterface<StudyAreaFieldConfiguration|null> $builder
   * @param array<string, mixed>                                   $options
   */
  private function skillFields(FormBuilderInterface $builder, array $options): void
  {
    if ($options['deprecated_skills']) {
      $builder
        ->add('learningOutcomeObjName', null, [
          'form_header' => 'learning-outcome._name',
          'label'       => 'field-configuration.obj-name',
          'help'        => 'field-configuration.obj-help',
          'attr'        => [
            'placeholder' => 'learning-outcome._name',
          ],
        ]);
    } else {
      $builder
        ->add('skillObjName', null, [
          'form_header' => 'skill._name',
          'label'       => 'field-configuration.obj-name',
          'help'        => 'field-configuration.obj-help',
          'attr'        => [
            'placeholder' => 'skill._name',
          ],
        ]);
    }
  }
}
