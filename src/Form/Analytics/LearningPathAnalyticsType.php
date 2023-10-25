<?php

namespace App\Form\Analytics;

use App\Analytics\Model\LearningPathVisualisationRequest;
use App\Entity\LearningPath;
use App\Entity\StudyArea;
use App\Repository\LearningPathRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningPathAnalyticsType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $studyArea = $options['study_area'];
    assert($studyArea instanceof StudyArea);

    $builder
      ->add('learningPath', EntityType::class, [
        'label'         => 'learning-path._name',
        'class'         => LearningPath::class,
        'select2'       => true,
        'choice_label'  => 'name',
        'query_builder' => fn (LearningPathRepository $repo) => $repo->findForStudyAreaQb($studyArea)
          ->orderBy('lp.name'),
        'full_width_label' => true,
      ])
      ->add('teachingMoment', DateType::class, [
        'label'            => 'analytics.teaching-moment',
        'full_width_label' => true,
        'widget'           => 'single_text',
      ])
      ->add('periodStart', DateTimeType::class, [
        'label'            => 'analytics.date-start',
        'full_width_label' => true,
        'widget'           => 'single_text',
      ])
      ->add('periodEnd', DateTimeType::class, [
        'label'            => 'analytics.date-end',
        'full_width_label' => true,
        'widget'           => 'single_text',
      ])
      ->add('generate', ButtonType::class, [
        'label' => 'analytics.generate',
        'icon'  => 'fa-cog',
        'attr'  => [
          'class' => 'btn-outline-primary',
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
      ->setRequired('study_area')
      ->setAllowedTypes('study_area', StudyArea::class)
      ->setDefault('data_class', LearningPathVisualisationRequest::class);
  }
}
