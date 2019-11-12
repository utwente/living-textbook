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
use App\Form\Type\HiddenEntityType;
use App\Form\Type\SaveType;
use App\Repository\AbbreviationRepository;
use App\Repository\ConceptRepository;
use App\Repository\ContributorRepository;
use App\Repository\ExternalResourceRepository;
use App\Repository\LearningOutcomeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class EditConceptType extends AbstractType
{

  /** @var TranslatorInterface */
  private $translator;

  /**
   * EditConceptType constructor.
   *
   * @param TranslatorInterface $translator
   */
  public function __construct(TranslatorInterface $translator)
  {
    $this->translator = $translator;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var Concept $concept */
    $concept   = $options['concept'];
    $studyArea = $concept->getStudyArea();
    $editing   = $concept->getId() !== NULL;
    $builder
        ->add('name', TextType::class, [
            'label'      => 'concept.name',
            'empty_data' => '',
            'disabled'   => in_array('name', $options['disabled_fields']),
        ])
        ->add('definition', TextareaType::class, [
            'label'      => 'concept.definition',
            'empty_data' => '',
            'required'   => false,
            'disabled'   => in_array('definition', $options['disabled_fields']),
        ])
        ->add('synonyms', TextType::class, [
            'label'      => 'concept.synonyms',
            'empty_data' => '',
            'required'   => false,
            'disabled'   => in_array('synonyms', $options['disabled_fields']),
        ])
        ->add('introduction', BaseDataTextType::class, [
            'label'      => 'concept.introduction',
            'data_class' => DataIntroduction::class,
            'studyArea'  => $studyArea,
            'required'   => false,
            'disabled'   => in_array('introduction', $options['disabled_fields']),
        ])
        // This field is also used by the ckeditor plugin for concept selection
        ->add('priorKnowledge', EntityType::class, [
            'label'         => 'concept.prior-knowledge',
            'class'         => Concept::class,
            'choice_label'  => 'name',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (ConceptRepository $conceptRepository) use ($concept) {
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
            'select2'       => true,
            'attr'          => [
                'data-ckeditor-selector' => 'concepts', // Register for ckeditor
            ],
            'disabled'      => in_array('priorKnowledge', $options['disabled_fields']),
        ])
        ->add('learningOutcomes', EntityType::class, [
            'label'         => 'concept.learning-outcomes',
            'class'         => LearningOutcome::class,
            'choice_label'  => 'shortName',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (LearningOutcomeRepository $learningOutcomeRepository) use ($concept) {
              return $learningOutcomeRepository->findForStudyAreaQb($concept->getStudyArea());
            },
            'select2'       => true,
            'disabled'      => in_array('learningOutcomes', $options['disabled_fields']),
        ])
        ->add('theoryExplanation', BaseDataTextType::class, [
            'label'      => 'concept.theory-explanation',
            'required'   => false,
            'data_class' => DataTheoryExplanation::class,
            'studyArea'  => $studyArea,
            'disabled'   => in_array('theoryExplanation', $options['disabled_fields']),
        ])
        ->add('howTo', BaseDataTextType::class, [
            'label'      => 'concept.how-to',
            'required'   => false,
            'data_class' => DataHowTo::class,
            'studyArea'  => $studyArea,
            'disabled'   => in_array('howTo', $options['disabled_fields']),
        ])
        ->add('examples', BaseDataTextType::class, [
            'label'      => 'concept.examples',
            'required'   => false,
            'data_class' => DataExamples::class,
            'studyArea'  => $studyArea,
            'disabled'   => in_array('examples', $options['disabled_fields']),
        ])
        ->add('externalResources', EntityType::class, [
            'label'         => 'concept.external-resources',
            'class'         => ExternalResource::class,
            'choice_label'  => 'title',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (ExternalResourceRepository $externalResourceRepository) use ($concept) {
              return $externalResourceRepository->findForStudyAreaQb($concept->getStudyArea());
            },
            'select2'       => true,
            'disabled'      => in_array('externalResources', $options['disabled_fields']),
        ])
        ->add('selfAssessment', BaseDataTextType::class, [
            'label'      => 'concept.self-assessment',
            'required'   => false,
            'data_class' => DataSelfAssessment::class,
            'studyArea'  => $studyArea,
            'disabled'   => in_array('selfAssessment', $options['disabled_fields']),
        ]);

    $otherConceptsAvailable = ($editing && $studyArea->getConcepts()->count() > 1) || (!$editing && !$studyArea->getConcepts()->isEmpty());
    $linkTypesAvailable     = !$studyArea->getRelationTypes()->isEmpty();
    if ($otherConceptsAvailable && $linkTypesAvailable) {
      $builder
          ->add('outgoingRelations', ConceptRelationsType::class, [
              'label'    => 'concept.outgoing-relations',
              'concept'  => $concept,
              'disabled' => in_array('relations', $options['disabled_fields']),
          ])
          ->add('incomingRelations', ConceptRelationsType::class, [
              'label'    => 'concept.incoming-relations',
              'concept'  => $concept,
              'incoming' => true,
              'disabled' => in_array('incomingRelations', $options['disabled_fields']),
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
        ])
        ->add('submit', SaveType::class, [
            'locate_static'       => true,
            'enable_cancel'       => true,
            'cancel_label'        => 'form.discard',
            'cancel_route'        => $editing ? 'app_concept_show' : 'app_concept_list',
            'cancel_route_params' => $editing ? ['concept' => $concept->getId()] : [],
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
        'data_class'      => Concept::class,
        'disabled_fields' => [],
    ]);

    $resolver->setAllowedTypes('concept', [Concept::class]);
    $resolver->setAllowedTypes('disabled_fields', 'string[]');
  }
}
