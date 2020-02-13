<?php

namespace App\Form\LearningOutcome;

use App\Entity\LearningOutcome;
use App\Entity\StudyArea;
use App\Form\Review\DisplayPendingChangeType;
use App\Form\Type\CkEditorType;
use App\Form\Type\SaveType;
use App\Review\Model\PendingChangeObjectInfo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditLearningOutcomeType extends AbstractType
{
  /**
   * @param FormBuilderInterface $builder
   * @param array                $options
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
    $pendingChangeObjectInfo = $options['pending_change_info'];
    $disabledFields          = $pendingChangeObjectInfo->getDisabledFields();

    $learningOutcome = $options['learningOutcome'];
    $editing         = $learningOutcome->getId() !== NULL;

    $builder
        ->add('number', NumberType::class, [
            'label'      => 'learning-outcome.number',
            'empty_data' => 0,
            'disabled'   => in_array('number', $disabledFields),
        ])
        ->add('number_review', DisplayPendingChangeType::class, [
            'field'               => 'number',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('name', TextType::class, [
            'label'      => 'learning-outcome.name',
            'empty_data' => '',
            'disabled'   => in_array('name', $disabledFields),
        ])
        ->add('name_review', DisplayPendingChangeType::class, [
            'field'               => 'name',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('text', CkEditorType::class, [
            'label'      => 'learning-outcome.text',
            'empty_data' => '',
            'studyArea'  => $options['studyArea'],
            'disabled'   => in_array('text', $disabledFields),
        ])
        ->add('text_review', DisplayPendingChangeType::class, [
            'field'               => 'text',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('submit', SaveType::class, [
            'enable_cancel'        => true,
            'enable_save_and_list' => true,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => $editing ? 'app_learningoutcome_show' : 'app_learningoutcome_list',
            'cancel_route_params'  => $editing ? ['learningOutcome' => $learningOutcome->getId()] : [],
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
        ->setRequired('learningOutcome')
        ->setAllowedTypes('learningOutcome', LearningOutcome::class)
        ->setDefault('data_class', LearningOutcome::class)
        ->setDefault('pending_change_info', new PendingChangeObjectInfo())
        ->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class);
  }
}
