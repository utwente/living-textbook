<?php

namespace App\Form\StudyArea;


use App\Entity\StudyArea;
use App\Entity\User;
use App\Form\Type\PrintedTextType;
use App\Form\Type\SaveType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
        ]);

    // User option
    if ($options['select_owner']) {
      $builder->add('owner', EntityType::class, [
          'label'   => 'study-area.owner',
          'class'   => User::class,
          'select2' => true,
      ]);
    } else {
      $builder->add('owner', PrintedTextType::class, [
          'label' => 'study-area.owner',
      ]);
    }

    $builder
        ->add('accessType', ChoiceType::class, [
            'label'        => 'study-area.access-type',
            'choices'      => StudyArea::getAccessTypes(),
            'choice_label' => function ($value, $key, $index) {
              return ucfirst($value);
            },
            'select2'      => true,
        ])
        ->add('submit', SaveType::class, [
            'enable_save_and_list' => !$options['save_only'],
            'enable_list'          => !$options['save_only'],
            'list_route'           => 'app_studyarea_list',
            'enable_cancel'        => !$options['save_only'],
            'cancel_label'         => 'form.discard',
            'cancel_route'         => $editing ? 'app_studyarea_show' : 'app_studyarea_list',
            'cancel_route_params'  => $editing ? ['studyArea' => $studyArea->getId()] : [],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setDefault('data_class', StudyArea::class)
        ->setDefault('save_only', false)
        ->setAllowedTypes('save_only', 'bool')
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setRequired('select_owner')
        ->setAllowedTypes('select_owner', 'bool');
  }

}
