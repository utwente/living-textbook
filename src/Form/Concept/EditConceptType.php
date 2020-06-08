<?php

namespace App\Form\Concept;

use App\Entity\Abbreviation;
use App\Entity\Concept;
use App\Entity\Contributor;
use App\Entity\Data\DataExamples;
use App\Entity\Data\DataHowTo;
use App\Entity\Data\DataIntroduction;
use App\Entity\Data\DataSelfAssessment;
use App\Entity\Data\DataTheoryExplanation;
use App\Entity\ExternalResource;
use App\Entity\LearningOutcome;
use App\Form\Data\BaseDataTextType;
use App\Form\Review\DisplayPendingChangeType;
use App\Form\Type\HiddenEntityType;
use App\Form\Type\SaveType;
use App\Naming\NamingService;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use App\Review\Model\PendingChangeObjectInfo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditConceptType extends AbstractType
{
  /**
   * @var NamingService
   */
  private $namingService;
  /**
   * @var TranslatorInterface
   */
  private $translator;

  public function __construct(TranslatorInterface $translator, NamingService $namingService)
  {
    $this->translator    = $translator;
    $this->namingService = $namingService;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $fieldNames = $this->namingService->get()->concept();

    /** @var PendingChangeObjectInfo $pendingChangeObjectInfo */
    $pendingChangeObjectInfo = $options['pending_change_info'];
    $disabledFields          = $pendingChangeObjectInfo->getDisabledFields();

    /** @var Concept $concept */
    $concept   = $options['concept'];
    $studyArea = $concept->getStudyArea();
    $editing   = $concept->getId() !== NULL;
    $builder
        ->add('name', TextType::class, [
            'label'      => 'concept.name',
            'empty_data' => '',
            'disabled'   => in_array('name', $disabledFields),
        ])
        ->add('name_review', DisplayPendingChangeType::class, [
            'field'               => 'name',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('instance', CheckboxType::class, [
            'label'    => 'concept.instance',
            'required' => false,
            'disabled' => in_array('instance', $disabledFields),
        ])
        ->add('instance_review', DisplayPendingChangeType::class, [
            'field'               => 'instance',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('definition', TextareaType::class, [
            'label'              => $fieldNames->definition(),
            'translation_domain' => false,
            'empty_data'         => '',
            'required'           => false,
            'disabled'           => in_array('definition', $disabledFields),
        ])
        ->add('definition_review', DisplayPendingChangeType::class, [
            'field'               => 'definition',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('introduction', BaseDataTextType::class, [
            'label'              => $fieldNames->introduction(),
            'translation_domain' => false,
            'data_class'         => DataIntroduction::class,
            'studyArea'          => $studyArea,
            'required'           => false,
            'disabled'           => in_array('introduction', $disabledFields),
        ])
        ->add('introduction_review', DisplayPendingChangeType::class, [
            'field'               => 'introduction',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('theoryExplanation', BaseDataTextType::class, [
            'label'              => $fieldNames->theoryExplanation(),
            'translation_domain' => false,
            'required'           => false,
            'data_class'         => DataTheoryExplanation::class,
            'studyArea'          => $studyArea,
            'disabled'           => in_array('theoryExplanation', $disabledFields),
        ])
        ->add('theoryExplanation_review', DisplayPendingChangeType::class, [
            'field'               => 'theoryExplanation',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('examples', BaseDataTextType::class, [
            'label'              => $fieldNames->examples(),
            'translation_domain' => false,
            'required'           => false,
            'data_class'         => DataExamples::class,
            'studyArea'          => $studyArea,
            'disabled'           => in_array('examples', $disabledFields),
        ])
        ->add('examples_review', DisplayPendingChangeType::class, [
            'field'               => 'examples',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('howTo', BaseDataTextType::class, [
            'label'              => $fieldNames->howTo(),
            'translation_domain' => false,
            'required'           => false,
            'data_class'         => DataHowTo::class,
            'studyArea'          => $studyArea,
            'disabled'           => in_array('howTo', $disabledFields),
        ])
        ->add('howTo_review', DisplayPendingChangeType::class, [
            'field'               => 'howTo',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('synonyms', TextType::class, [
            'label'              => $fieldNames->synonyms(),
            'translation_domain' => false,
            'empty_data'         => '',
            'required'           => false,
            'disabled'           => in_array('synonyms', $disabledFields),
        ])
        ->add('synonyms_review', DisplayPendingChangeType::class, [
            'field'               => 'synonyms',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('externalResources', EntityType::class, [
            'label'         => 'concept.external-resources',
            'class'         => ExternalResource::class,
            'choice_label'  => 'title',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (ExternalResourceRepository $externalResourceRepository) use ($studyArea) {
              return $externalResourceRepository->findForStudyAreaQb($studyArea);
            },
            'select2'       => true,
            'disabled'      => in_array('externalResources', $disabledFields),
        ])
        ->add('externalResources_review', DisplayPendingChangeType::class, [
            'field'               => 'externalResources',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('learningOutcomes', EntityType::class, [
            'label'         => 'concept.learning-outcomes',
            'class'         => LearningOutcome::class,
            'choice_label'  => 'shortName',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (LearningOutcomeRepository $learningOutcomeRepository) use ($studyArea) {
              return $learningOutcomeRepository->findForStudyAreaQb($studyArea);
            },
            'select2'       => true,
            'disabled'      => in_array('learningOutcomes', $disabledFields),
        ])
        ->add('learningOutcomes_review', DisplayPendingChangeType::class, [
            'field'               => 'learningOutcomes',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])

        // This field is also used by the ckeditor plugin for concept selection
        ->add('priorKnowledge', EntityType::class, [
            'label'              => $fieldNames->priorKnowledge(),
            'translation_domain' => false,
            'class'              => Concept::class,
            'choice_label'       => 'name',
            'required'           => false,
            'multiple'           => true,
            'query_builder'      => function (ConceptRepository $conceptRepository) use ($concept) {
              $qb = $conceptRepository->createQueryBuilder('c');

              if ($concept->getId()) {
                $qb->where('c != :self')
                    ->setParameter('self', $concept);
              }

              $qb->andWhere('c.studyArea = :studyArea')
                  ->setParameter('studyArea', $concept->getStudyArea())
                  ->orderBy('c.name');

              return $qb;
            },
            'select2'            => true,
            'attr'               => [
                'data-ckeditor-selector' => 'concepts', // Register for ckeditor
            ],
            'disabled'           => in_array('priorKnowledge', $disabledFields),
        ])
        ->add('priorKnowledge_review', DisplayPendingChangeType::class, [
            'field'               => 'priorKnowledge',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('selfAssessment', BaseDataTextType::class, [
            'label'      => $fieldNames->selfAssessment(),
            'required'   => false,
            'data_class' => DataSelfAssessment::class,
            'studyArea'  => $studyArea,
            'disabled'   => in_array('selfAssessment', $disabledFields),
        ])
        ->add('selfAssessment_review', DisplayPendingChangeType::class, [
            'field'               => 'selfAssessment',
            'pending_change_info' => $pendingChangeObjectInfo,
        ]);

    $otherConceptsAvailable = ($editing && $studyArea->getConcepts()->count() > 1) || (!$editing && !$studyArea->getConcepts()->isEmpty());
    $linkTypesAvailable     = !$studyArea->getRelationTypes()->isEmpty();
    if ($otherConceptsAvailable && $linkTypesAvailable) {
      $builder
          ->add('outgoingRelations', ConceptRelationsType::class, [
              'label'    => 'concept.outgoing-relations',
              'concept'  => $concept,
              'disabled' => in_array('relations', $disabledFields),
          ])
          ->add('outgoingRelations_review', DisplayPendingChangeType::class, [
              'field'               => 'relations',
              'pending_change_info' => $pendingChangeObjectInfo,
          ])
          ->add('incomingRelations', ConceptRelationsType::class, [
              'label'    => 'concept.incoming-relations',
              'concept'  => $concept,
              'incoming' => true,
              'disabled' => in_array('incomingRelations', $disabledFields),
          ])
          ->add('incomingRelations_review', DisplayPendingChangeType::class, [
              'field'               => 'incomingRelations',
              'pending_change_info' => $pendingChangeObjectInfo,
          ]);
    } else {
      $builder->add('relations', TextType::class, [
          'label'    => 'concept.relations',
          'disabled' => true,
          'mapped'   => false,
          'required' => false,
          'data'     => $this->translator->trans('concept.no-relations-possible-' . ($otherConceptsAvailable ? "linktype" : "concept")),
      ]);
    }

    $builder
        ->add('contributors', EntityType::class, [
            'label'         => 'concept.contributors',
            'class'         => Contributor::class,
            'choice_label'  => 'name',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (ContributorRepository $contributorRepository) use ($concept) {
              return $contributorRepository->findForStudyAreaQb($concept->getStudyArea());
            },
            'select2'       => true,
            'disabled'      => in_array('contributors', $disabledFields),
        ])
        ->add('contributors_review', DisplayPendingChangeType::class, [
            'field'               => 'contributors',
            'pending_change_info' => $pendingChangeObjectInfo,
        ])
        ->add('submit', SaveType::class, [
            'locate_static'        => true,
            'enable_cancel'        => true,
            'enable_save_and_list' => $options['enable_save_and_list'],
            'cancel_label'         => 'form.discard',
            'cancel_route'         => $options['cancel_route'] ?? ($editing ? 'app_concept_show' : 'app_concept_list'),
            'cancel_route_params'  => $options['cancel_route']
                ? ($options['cancel_route_params'] ?? [])
                : ($editing ? ['concept' => $concept->getId()] : []),
        ]);

    // Fields below are hidden fields, which are used for ckeditor plugins to have the data available on the page
    // Also used (from above): priorKnowledge
    $builder
        ->add('abbreviations', HiddenEntityType::class, [
            'class'         => Abbreviation::class,
            'choice_label'  => 'abbreviation',
            'required'      => false,
            'mapped'        => false,
            'query_builder' => function (AbbreviationRepository $abbreviationRepository) use ($concept) {
              return $abbreviationRepository->createQueryBuilder('a')
                  ->where('a.studyArea = :studyArea')
                  ->setParameter('studyArea', $concept->getStudyArea())
                  ->orderBy('a.abbreviation');
            },
            'attr'          => [
                'data-ckeditor-selector' => 'abbreviations', // Register for ckeditor
            ],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('concept');
    $resolver->setDefaults([
        'data_class'           => Concept::class,
        'pending_change_info'  => new PendingChangeObjectInfo(),
        'enable_save_and_list' => true,
        'cancel_route'         => NULL,
        'cancel_route_params'  => NULL,
    ]);

    $resolver->setAllowedTypes('concept', [Concept::class]);
    $resolver->setAllowedTypes('pending_change_info', PendingChangeObjectInfo::class);
    $resolver->setAllowedTypes('enable_save_and_list', 'bool');
    $resolver->setAllowedTypes('cancel_route', ['null', 'string']);
    $resolver->setAllowedTypes('cancel_route_params', ['null', 'array']);
  }
}
