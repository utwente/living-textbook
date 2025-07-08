<?php

namespace App\Form\LearningPath;

use App\Entity\Concept;
use App\Entity\LearningOutcome;
use App\Entity\StudyArea;
use App\Naming\NamingService;
use App\Repository\ConceptRepository;
use App\Repository\LearningOutcomeRepository;
use JMS\Serializer\SerializerInterface;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function ucfirst;

class LearningPathElementSelectorType extends AbstractType
{
  public function __construct(
    private readonly LearningOutcomeRepository $learningOutcomeRepository,
    private readonly SerializerInterface $serializer,
    private readonly NamingService $namingService)
  {
  }

  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $studyArea = $options['studyArea'];

    $builder
      ->add('concepts', EntityType::class, [
        'label'               => 'menu.concept',
        'class'               => Concept::class,
        'choice_label'        => 'name',
        'required'            => false,
        'multiple'            => true,
        'query_builder'       => fn (ConceptRepository $conceptRepository) => $conceptRepository->findForStudyAreaOrderByNameQb($studyArea),
        'select2'             => true,
        'select2_placeholder' => 'learning-path.select',
      ])
      ->add('learningOutcomes', EntityType::class, [
        'label'               => ucfirst($this->namingService->get()->learningOutcome()->objs()),
        'class'               => LearningOutcome::class,
        'choice_label'        => 'name',
        'required'            => false,
        'multiple'            => true,
        'query_builder'       => fn (LearningOutcomeRepository $learningOutcomeRepository) => $learningOutcomeRepository->findForStudyAreaQb($studyArea),
        'select2'             => true,
        'select2_placeholder' => 'learning-path.select',
      ])
      ->add('learningOutcomesConcepts', HiddenType::class, [
        'data' => $this->serializer->serialize($this->learningOutcomeRepository->findUsedConceptIdsForStudyArea($studyArea), 'json'),
      ])
      ->add('add', ButtonType::class, [
        'label' => 'learning-path.add-element',
        'icon'  => 'fa-plus',
        'attr'  => [
          'class'    => 'btn-outline-success float-right',
          'disabled' => 'disabled',
          'onclick'  => 'addLearningPathConcepts_' . $options['sortable_id'] . '();',
        ],
      ]);
  }

  #[Override]
  public function buildView(FormView $view, FormInterface $form, array $options): void
  {
    $view->vars['sortable_id'] = $options['sortable_id'];
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver
      ->setRequired('studyArea')
      ->setAllowedTypes('studyArea', StudyArea::class)
      ->setRequired('sortable_id')
      ->setAllowedTypes('sortable_id', 'string');
  }
}
