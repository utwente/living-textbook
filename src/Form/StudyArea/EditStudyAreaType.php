<?php

namespace App\Form\StudyArea;

use App\Entity\StudyArea;
use App\Form\Type\CkEditorType;
use App\Form\Type\SaveType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditStudyAreaType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $studyArea = $options['studyArea'];
    $editing   = $studyArea->getId() !== NULL;
    $builder
        ->add('name', TextType::class, [
            'label'      => 'study-area.name',
            'empty_data' => '',
        ])
        ->add('accessType', ChoiceType::class, [
            'label'        => 'study-area.access-type',
            'help'         => 'study-area.access-type-change-note',
            'choices'      => StudyArea::getAccessTypes(),
            'choice_label' => function ($value, $key, $index) {
              return ucfirst($value);
            },
            'select2'      => true,
        ])
        ->add('description', CkEditorType::class, [
            'label'     => 'study-area.description',
            'required'  => false,
            'studyArea' => $studyArea,
        ])
        ->add('printHeader', TextType::class, [
            'label'    => 'study-area.print-header',
            'help'     => 'study-area.print-header-help',
            'required' => false,
        ])
        ->add('printIntroduction', TextareaType::class, [
            'label'    => 'study-area.print-introduction',
            'help'     => 'study-area.print-introduction-help',
            'required' => false,
        ])
        ->add('trackUsers', CheckboxType::class, [
            'label'    => 'study-area.track-users',
            'help'     => 'study-area.track-users-help',
            'required' => false,
        ]);
    if (!$options['hide_submit']) {
      $builder
          ->add('submit', SaveType::class, [
              'enable_save_and_list' => !$options['save_only'] && $options['save_and_list'],
              'save_and_list_label'  => 'form.save-and-dashboard',
              'enable_cancel'        => !$options['save_only'],
              'cancel_label'         => 'form.discard',
              'cancel_route'         => $editing ? $options['cancel_route_edit'] : $options['cancel_route'],
              'cancel_route_params'  => [],
          ]);
    }
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefaults([
            'data_class'        => StudyArea::class,
            'save_only'         => false,
            'save_and_list'     => true,
            'cancel_route'      => 'app_studyarea_list',
            'cancel_route_edit' => 'app_default_dashboard',
            'hide_submit'       => false,
        ])
        ->setAllowedTypes('save_only', 'bool')
        ->setAllowedTypes('save_and_list', 'bool')
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setAllowedTypes('cancel_route', 'string')
        ->setAllowedTypes('cancel_route_edit', 'string')
        ->setRequired('select_owner')
        ->setAllowedTypes('select_owner', 'bool')
        ->setAllowedTypes('hide_submit', 'bool');
  }

}
