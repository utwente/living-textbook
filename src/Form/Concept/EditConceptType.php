<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use App\Entity\Data\DataExamples;
use App\Entity\Data\DataHowTo;
use App\Entity\Data\DataIntroduction;
use App\Entity\Data\DataLearningOutcomes;
use App\Entity\Data\DataSelfAssessment;
use App\Entity\Data\DataTheoryExplanation;
use App\Form\Data\BaseDataTextType;
use App\Form\Data\DataExternalResourcesType;
use App\Form\Type\SaveType;
use App\Repository\ConceptRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditConceptType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    /** @var Concept $concept */
    $concept = $options['concept'];
    $editing = $concept->getId() !== NULL;
    $builder
        ->add('name', TextType::class, [
            'label' => 'concept.name',
        ])
        ->add('introduction', BaseDataTextType::class, [
            'label'      => 'concept.introduction',
            'required'   => true,
            'data_class' => DataIntroduction::class,
        ])
        ->add('priorKnowledge', EntityType::class, [
            'label'         => 'concept.prior-knowledge',
            'class'         => Concept::class,
            'choice_label'  => 'name',
            'required'      => false,
            'multiple'      => true,
            'query_builder' => function (ConceptRepository $conceptRepository) use ($concept) {
              return $conceptRepository->createQueryBuilder('c')
                  ->where('c != :self')
                  ->andWhere('c.studyArea = :studyArea')
                  ->setParameter('self', $concept)
                  ->setParameter('studyArea', $concept->getStudyArea())
                  ->orderBy('c.name');
            },
            'select2'       => true,
        ])
        ->add('learningOutcomes', BaseDataTextType::class, [
            'label'      => 'concept.learning-outcomes',
            'required'   => false,
            'data_class' => DataLearningOutcomes::class,
        ])
        ->add('theoryExplanation', BaseDataTextType::class, [
            'label'      => 'concept.theory-explanation',
            'required'   => false,
            'data_class' => DataTheoryExplanation::class,
        ])
        ->add('howTo', BaseDataTextType::class, [
            'label'      => 'concept.how-to',
            'required'   => false,
            'data_class' => DataHowTo::class,
        ])
        ->add('examples', BaseDataTextType::class, [
            'label'      => 'concept.examples',
            'required'   => false,
            'data_class' => DataExamples::class,
        ])
        ->add('externalResources', DataExternalResourcesType::class, [
            'label'    => 'concept.external-resources',
            'required' => true,
        ])
        ->add('selfAssessment', BaseDataTextType::class, [
            'label'      => 'concept.self-assessment',
            'required'   => false,
            'data_class' => DataSelfAssessment::class,
        ])
        ->add('outgoingRelations', ConceptRelationsType::class, [
            'label'   => 'concept.outgoing-relations',
            'concept' => $concept,
        ])
        ->add('incomingRelations', ConceptRelationsType::class, [
            'label'    => 'concept.incoming-relations',
            'concept'  => $concept,
            'incoming' => true,
        ])
        ->add('submit', SaveType::class, [
            'list_route'          => 'app_concept_list',
            'enable_cancel'       => true,
            'cancel_label'        => 'form.discard',
            'cancel_route'        => $editing ? 'app_concept_show' : 'app_concept_list',
            'cancel_route_params' => $editing ? ['concept' => $concept->getId()] : [],
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('concept');
    $resolver->setDefaults([
        'data_class' => Concept::class,
    ]);

    $resolver->setAllowedTypes('concept', [Concept::class]);
  }
}
