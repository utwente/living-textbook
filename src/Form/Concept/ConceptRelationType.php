<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use App\Repository\ConceptRepository;
use App\Repository\RelationTypeRepository;
use Override;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConceptRelationType extends AbstractType
{
  public function __construct(private readonly TranslatorInterface $translator)
  {
  }

  #[Override]
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    /** @var Concept $concept */
    $concept  = $options['concept'];
    $incoming = $options['incoming'];

    if ($incoming) {
      $this->addConceptType($builder, 'source', $concept);
    } else {
      $this->addTextType($builder, 'source', $concept->getName());
    }

    if (!$incoming) {
      $this->addConceptType($builder, 'target', $concept);
    } else {
      $this->addTextType($builder, 'target', $concept->getName());
    }

    $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($concept) {
      /** @var ConceptRelation|null $relation */
      $relation       = $event->getData();
      $relationTypeId = $relation === null ? null : $relation->getRelationType()->getId();
      $form           = $event->getForm();

      $form->add('relationType', EntityType::class, [
        'label'         => 'relation.type',
        'class'         => RelationType::class,
        'select2'       => true,
        'choice_label'  => 'name',
        'choice_attr'   => static fn (RelationType $val, $key, $index) => $val->getDeletedAt() === null ? [] : ['disabled' => 'disabled'],
        'query_builder' => static function (RelationTypeRepository $repo) use ($concept, $relationTypeId) {
          $qb = $repo->createQueryBuilder('rt');

          // Update result based on current data
          if ($relationTypeId === null) {
            $qb->where('rt.deletedAt IS NULL');
          } else {
            $qb->where($qb->expr()->orX(
              $qb->expr()->isNull('rt.deletedAt'),
              $qb->expr()->eq('rt.id', ':id')
            ));
            $qb->setParameter('id', $relationTypeId);
          }

          $qb->andWhere('rt.studyArea = :studyArea')
            ->setParameter('studyArea', $concept->getStudyArea());

          return $qb;
        },
      ]);
    });

    // Add a transformer to create new relations on every edit
    $builder->addModelTransformer(new CallbackTransformer(static function (?ConceptRelation $conceptRelation) {
      if ($conceptRelation) {
        return [
          'source'       => $conceptRelation->getSource(),
          'target'       => $conceptRelation->getTarget(),
          'relationType' => $conceptRelation->getRelationType(),
        ];
      }

      return [
        'source'       => null,
        'target'       => null,
        'relationType' => null,
      ];
    }, static function ($data) use ($concept, $incoming) {
      $conceptRelation = new ConceptRelation()
        ->setRelationType($data['relationType']);

      // Create correct data
      if ($incoming) {
        $conceptRelation
          ->setSource($data['source'])
          ->setTarget($concept);
      } else {
        $conceptRelation
          ->setSource($concept)
          ->setTarget($data['target']);
      }

      return $conceptRelation;
    }));
  }

  private function addConceptType(FormBuilderInterface $builder, string $field, Concept $concept): void
  {
    $builder
      ->add($field, EntityType::class, [
        'label'         => 'relation.' . $field,
        'class'         => Concept::class,
        'choice_label'  => 'name',
        'select2'       => true,
        'query_builder' => static fn (ConceptRepository $repo) => $repo->createQueryBuilder('c')
          ->where('c.id != :id')
          ->andWhere('c.studyArea = :studyArea')
          ->orderBy('c.name', 'ASC')
          ->setParameter('id', $concept->getId() ?? 0)
          ->setParameter('studyArea', $concept->getStudyArea()),
      ]);
  }

  private function addTextType(FormBuilderInterface $builder, string $field, string $name): void
  {
    $builder->add($field, TextType::class, [
      'label'    => 'relation.' . $field,
      'disabled' => true,
      'mapped'   => false,
      'required' => false,
      'data'     => empty($name) ? $this->translator->trans('concept.new') : $name,
    ]);
  }

  #[Override]
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setRequired('concept');
    $resolver->setDefaults([
      'incoming' => false,
    ]);

    $resolver->setAllowedTypes('concept', [Concept::class]);
    $resolver->setAllowedTypes('incoming', ['bool']);
  }
}
