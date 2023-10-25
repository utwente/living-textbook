<?php

namespace App\Form\LearningPath;

use App\Entity\Abbreviation;
use App\Entity\Concept;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\Form\Review\DisplayPendingChangeType;
use App\Form\Type\CkEditorType;
use App\Form\Type\HiddenEntityType;
use App\Form\Type\SaveType;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRepository;
use App\Review\Model\PendingChangeObjectInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditLearningPathType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
    $pendingChangeObjectInfo = $options['pending_change_info'];
    $disabledFields          = $pendingChangeObjectInfo->getDisabledFields();

    $learningPath = $options['learningPath'];
    $editing      = $learningPath->getId() !== null;

    $builder
      ->add('name', TextType::class, [
        'label'      => 'learning-path.name',
        'disabled'   => in_array('name', $disabledFields),
        'empty_data' => '',
      ])
      ->add('name_review', DisplayPendingChangeType::class, [
        'field'               => 'name',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('introduction', CkEditorType::class, [
        'label'       => 'learning-path.introduction',
        'studyArea'   => $options['studyArea'],
        'config_name' => 'ltb_concept_config',
        'disabled'    => in_array('introduction', $disabledFields),
      ])
      ->add('introduction_review', DisplayPendingChangeType::class, [
        'field'               => 'introduction',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('question', TextareaType::class, [
        'label'      => 'learning-path.question',
        'disabled'   => in_array('question', $disabledFields),
        'empty_data' => '',
      ])
      ->add('question_review', DisplayPendingChangeType::class, [
        'field'               => 'question',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('elements', LearningPathElementContainerType::class, [
        'label'          => 'learning-path.elements',
        'studyArea'      => $options['studyArea'],
        'learningPath'   => $learningPath,
        'error_bubbling' => false,
        'disabled'       => in_array('elements', $disabledFields),
      ])
      ->add('elements_review', DisplayPendingChangeType::class, [
        'field'               => 'elements',
        'pending_change_info' => $pendingChangeObjectInfo,
      ])
      ->add('submit', SaveType::class, [
        'enable_cancel'        => true,
        'enable_save_and_list' => true,
        'cancel_label'         => 'form.discard',
        'cancel_route'         => $editing ? 'app_learningpath_show' : 'app_learningpath_list',
        'cancel_route_params'  => $editing ? ['learningPath' => $learningPath->getId()] : [],
      ]);

    // Fields below are hidden fields, which are used for ckeditor plugins to have the data available on the page
    $builder
      ->add('abbreviations', HiddenEntityType::class, [
        'class'         => Abbreviation::class,
        'choice_label'  => 'abbreviation',
        'required'      => false,
        'mapped'        => false,
        'query_builder' => fn (AbbreviationRepository $abbreviationRepository) => $abbreviationRepository->findForStudyAreaQb($options['studyArea']),
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
        'query_builder' => fn (ConceptRepository $conceptRepository) => $conceptRepository->findForStudyAreaOrderByNameQb($options['studyArea']),
        'attr'          => [
          'data-ckeditor-selector' => 'concepts', // Register for ckeditor
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setRequired('studyArea')
      ->setAllowedTypes('studyArea', StudyArea::class)
      ->setRequired('learningPath')
      ->setAllowedTypes('learningPath', LearningPath::class)
      ->setDefault('data_class', LearningPath::class)
      ->setDefault('pending_change_info', new PendingChangeObjectInfo())
      ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class);
  }
}
