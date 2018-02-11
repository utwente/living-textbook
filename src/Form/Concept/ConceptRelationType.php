<?php

namespace App\Form\Concept;

use App\Entity\Concept;
use App\Entity\ConceptRelation;
use App\Entity\RelationType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConceptRelationType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->add('source', EntityType::class, [
            'label'        => 'relation.source',
            'class'        => Concept::class,
            'choice_label' => 'name',
        ])
        ->add('relationType', EntityType::class, [
            'label'        => 'relation.type',
            'class'        => RelationType::class,
            'choice_label' => 'name',
        ])
        ->add('target', EntityType::class, [
            'label'        => 'relation.target',
            'class'        => Concept::class,
            'choice_label' => 'name',
        ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefault('data_class', ConceptRelation::class);
  }

}
