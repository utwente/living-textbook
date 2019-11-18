<?php

namespace App\Form\LearningOutcome;

use App\Entity\LearningOutcome;
use App\Entity\StudyArea;
use App\Form\Type\CkEditorType;
use App\Form\Type\SaveType;
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
    $learningOutcome = $options['learningOutcome'];
    $editing         = $learningOutcome->getId() !== NULL;
    $builder
        ->add('number', NumberType::class, [
            'label'      => 'learning-outcome.number',
            'empty_data' => 0,
        ])
        ->add('name', TextType::class, [
            'label'      => 'learning-outcome.name',
            'empty_data' => '',
        ])
        ->add('text', CkEditorType::class, [
            'label'      => 'learning-outcome.text',
            'empty_data' => '',
            'studyArea'  => $options['studyArea'],
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
        ->setDefault('data_class', LearningOutcome::class);
  }
}
