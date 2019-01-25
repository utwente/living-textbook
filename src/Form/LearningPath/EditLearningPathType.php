<?php

namespace App\Form\LearningPath;

use App\Entity\Abbreviation;
use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\Form\Type\CkEditorType;
use App\Form\Type\HiddenEntityType;
use App\Form\Type\SaveType;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditLearningPathType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('name', TextType::class, [
            'label' => 'learning-path.name',
        ])
        ->add('introduction', CkEditorType::class, [
            'label'       => 'learning-path.introduction',
            'studyArea'   => $options['studyArea'],
            'config_name' => 'ltb_concept_config',
        ])
        ->add('question', TextareaType::class, [
            'label' => 'learning-path.question',
        ])
        ->add('elements', LearningPathElementContainerType::class, [
            'label'        => 'learning-path.elements',
            'studyArea'    => $options['studyArea'],
            'learningPath' => $options['learningPath'],
        ])
        ->add('submit', SaveType::class, [
            'list_route'           => 'app_learningpath_list',
            'enable_cancel'        => true,
            'enable_save_and_list' => $options['save-and-list'],
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_learningpath_list',
        ]);

    // Fields below are hidden fields, which are used for ckeditor plugins to have the data available on the page
    $builder
        ->add('abbreviations', HiddenEntityType::class, [
            'class'         => Abbreviation::class,
            'choice_label'  => 'abbreviation',
            'required'      => false,
            'mapped'        => false,
            'query_builder' => function (AbbreviationRepository $abbreviationRepository) use ($options) {
              return $abbreviationRepository->findForStudyAreaQb($options['studyArea']);
            },
            'attr'          => [
                'data-ckeditor-selector' => 'abbreviations', // Register for ckeditor
            ],
        ])
        ->add('concepts', HiddenEntityType::class, [
            'label'         => 'concept.prior-knowledge',
            'class'         => Concept::class,
            'choice_label'  => 'name',
            'required'      => false,
            'mapped'        => false,
            'query_builder' => function (ConceptRepository $conceptRepository) use ($options) {
              return $conceptRepository->findForStudyAreaOrderByNameQb($options['studyArea']);
            },
            'attr'          => [
                'data-ckeditor-selector' => 'concepts', // Register for ckeditor
            ],
        ]);
  }

  /**
   * @param OptionsResolver $resolver
   */
  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setRequired('learningPath')
        ->setAllowedTypes('learningPath', LearningPath::class)
        ->setDefault('data_class', LearningPath::class)
        ->setDefault('save-and-list', false);
  }
}
