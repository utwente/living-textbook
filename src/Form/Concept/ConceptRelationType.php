<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use App\Repository\ConceptRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

class ConceptRelationType extends AbstractType
{
  /** @var TranslatorInterface */
  private $translator;

  /**
   * ConceptRelationType constructor.
   *
   * @param TranslatorInterface $translator
   */
  public function __construct(TranslatorInterface $translator)
  {
    $this->translator = $translator;
  }

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    if ($options['incoming']) {
      self::addEntityType($builder, 'source', $options['concept_id']);
    } else {
      $this->addTextType($builder, 'source', $options['concept_name']);
    }

    $builder->add('relationType', EntityType::class, [
        'label'        => 'relation.type',
        'class'        => RelationType::class,
        'choice_label' => 'name',
    ]);

    if (!$options['incoming']) {
      $this->addEntityType($builder, 'target', $options['concept_id']);
    } else {
      $this->addTextType($builder, 'target', $options['concept_name']);
    }
  }

  private function addEntityType(FormBuilderInterface $builder, string $field, ?int $id)
  {
    $builder
        ->add($field, EntityType::class, [
            'label'         => 'relation.' . $field,
            'class'         => Concept::class,
            'choice_label'  => 'name',
            'query_builder' => function (ConceptRepository $repo) use ($id) {
              return $repo->createQueryBuilder('c')
                  ->where('c.id != :id')
                  ->orderBy('c.name', 'ASC')
                  ->setParameter('id', $id ?? 0);
            },
        ]);
  }

  private function addTextType(FormBuilderInterface $builder, string $field, string $name)
  {
    $builder->add($field, TextType::class, [
        'label'    => 'relation.' . $field,
        'disabled' => true,
        'mapped'   => false,
        'required' => false,
        'data'     => empty($name) ? $this->translator->trans('concept.new') : $name,
    ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setRequired('concept_name');
    $resolver->setRequired('concept_id');
    $resolver->setDefaults([
        'incoming'   => false,
        'data_class' => ConceptRelation::class,
    ]);

    $resolver->setAllowedTypes('concept_id', ['null', 'int']);
    $resolver->setAllowedTypes('concept_name', ['string']);
    $resolver->setAllowedTypes('incoming', ['bool']);

  }

}
