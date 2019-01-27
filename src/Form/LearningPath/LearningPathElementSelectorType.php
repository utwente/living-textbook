<?php

namespace App\Form\LearningPath;

use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\StudyArea;
use App\Repository\ConceptRepository;
use App\Repository\LearningOutcomeRepository;
use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LearningPathElementSelectorType extends AbstractType
{
  /** @var LearningOutcomeRepository */
  private $learningOutcomeRepository;
  /** @var SerializerInterface */
  private $serializer;

  public function __construct(LearningOutcomeRepository $learningOutcomeRepository, SerializerInterface $serializer)
  {
    $this->learningOutcomeRepository = $learningOutcomeRepository;
    $this->serializer                = $serializer;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $studyArea = $options['studyArea'];

    $builder
        ->add('concepts', EntityType::class, [
            'label'         => 'menu.concept',
            'class'         => Concept::class,
            'choice_label'  => 'name',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (ConceptRepository $conceptRepository) use ($studyArea) {
              return $conceptRepository->findForStudyAreaOrderByNameQb($studyArea);
            },
            'select2'       => true,
        ])
        ->add('learningOutcomes', EntityType::class, [
            'label'         => 'menu.learning-outcomes',
            'class'         => LearningOutcome::class,
            'choice_label'  => 'name',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (LearningOutcomeRepository $learningOutcomeRepository) use ($studyArea) {
              return $learningOutcomeRepository->findForStudyAreaQb($studyArea);
            },
            'select2'       => true,
        ])
        ->add('learningOutcomesConcepts', HiddenType::class, [
            'data' => $this->serializer->serialize($this->learningOutcomeRepository->findUsedConceptIdsForStudyArea($studyArea), 'json'),
        ])
        ->add('add', ButtonType::class, [
            'label' => 'form.add',
            'icon'  => 'fa-plus',
            'attr'  => [
                'class'   => 'btn-outline-success float-right',
                'onclick' => 'addLearningPathConcepts_' . $options['sortable_id'] . '();',
            ],
        ]);
  }

  public function buildView(FormView $view, FormInterface $form, array $options)
  {
    $view->vars['sortable_id'] = $options['sortable_id'];
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver
        ->setRequired('studyArea')
        ->setAllowedTypes('studyArea', StudyArea::class)
        ->setRequired('sortable_id')
        ->setAllowedTypes('sortable_id', 'string');
  }

}
