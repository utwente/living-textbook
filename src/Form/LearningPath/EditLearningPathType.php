<?php

namespace App\Form\LearningPath;

use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\Form\Type\SaveType;
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
