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
    $builder
        ->add('number', NumberType::class, [
            'label' => 'learning-outcome.number',
        ])
        ->add('name', TextType::class, [
            'label' => 'learning-outcome.name',
        ])
        ->add('text', CkEditorType::class, [
            'label'     => 'learning-outcome.text',
            'studyArea' => $options['studyArea'],
        ])
        ->add('submit', SaveType::class, [
            'list_route'           => 'app_learningoutcome_list',
            'enable_cancel'        => true,
            'enable_save_and_list' => false,
            'cancel_label'         => 'form.discard',
            'cancel_route'         => 'app_learningoutcome_list',
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
        ->setDefault('data_class', LearningOutcome::class);
  }
}
